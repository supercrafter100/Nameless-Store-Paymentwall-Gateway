<?php
/**
 * PaymentWall_Gateway class
 *
 * @package Modules\Store
 * @author Supercrafter100
 * @version 2.0.2
 * @license MIT
 */
class PaymentWall_Gateway extends GatewayBase {
    public function __construct()
    {
        require_once(ROOT_PATH . '/modules/Store/gateways/PaymentWall/lib/paymentwall-php/lib/paymentwall.php');

        $name = 'PaymentWall';
        $settings = ROOT_PATH . '/modules/Store/gateways/PaymentWall/gateway_settings/settings.php';
        $author = '<a href="https://github.com/supercrafter100/" target="_blank" rel="nofollow noopener">Supercrafter100</a>';
        $gateway_version = '1.5.2';
        $store_version = '1.5.2';

        parent::__construct($name, $author, $gateway_version, $store_version, $settings);
    }

    public function onCheckoutPageLoad(TemplateBase $template, Customer $customer): void
    {
        // Not necessary
    }

    public function processOrder(Order $order): void
    {
        // Load api
        $this->getApiContext();
        $widget_code = StoreConfig::get('paymentwall/widget_id') ?? 'p1';

        $currency = $order->getAmount()->getCurrency();
        $successRedirect = rtrim(URL::getSelfURL(), '/') . URL::build('/store/process/', 'gateway=PaymentWall&do=success');

        $products = [
            new Paymentwall_Product(
                substr(md5(mt_rand()), 0, 7),
                Store::fromCents($order->getAmount()->getTotalCents()),
                $currency,
                'Order #' . $order->data()->id
            )
        ];

        $widget = new Paymentwall_Widget(
            $order->customer()->data()->id,
            $widget_code,
            $products,
            [
                'success_url' => $successRedirect,
                'project_name' => Output::getClean(SITE_NAME),
                'merchant_order_id' => $order->data()->id,
                'order_id' => $order->data()->id,
                'evaluation' => true
            ]
        );

        Redirect::to($widget->getUrl());
    }

    private function getApiContext()
    {
        $public_key = StoreConfig::get('paymentwall/public_key');
        $private_key = StoreConfig::get('paymentwall/private_key');

        if ($public_key && $private_key) {
            Paymentwall_Config::getInstance()->set([
                'api_type' => Paymentwall_Config::API_GOODS,
                'public_key' => $public_key,
                'private_key' => $private_key
            ]);
        } else {
            $this->addError('Administration has not completed configuration of this gateway!');
        }
    }

    public function handleReturn(): bool
    {
        if (isset($_GET['do']) && $_GET['do'] == 'success') {
            return true;
        }

        return false;
    }

    public function handleListener(): void
    {
        $this->getApiContext();

        $pingback = new Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
        $order = $_GET['order_id'];

        if ($pingback->validate(true)) {
            if ($pingback->isDeliverable()) {
                $payment = new Payment($order, 'payment_id');
                $payment->handlePaymentEvent('COMPLETED', [
                    'order_id' => $order,
                    'gateway_id' => $this->getId(),
                    'payment_id' => $order,
                    'transaction' => $order,
                    'amount_cents' => Store::toCents($pingback->getProduct()->getAmount()),
                    'currency' => $pingback->getProduct()->getCurrencyCode(),
                ]);
            }

            if ($pingback->isCancelable()) {
                $payment = new Payment($order, 'payment_id');
                if ($payment->exists()) {
                    $payment->handlePaymentEvent('REVERSED');
                }
            }

            if ($pingback->isUnderReview()) {
                $payment = new Payment($order, 'payment_id');
                if ($payment->exists()) {
                    $payment->handlePaymentEvent('PENDING');
                }
            }
        }
        die("OK");
    }
}

$gateway = new PaymentWall_Gateway();
