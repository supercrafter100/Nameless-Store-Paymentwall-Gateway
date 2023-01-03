<?php

/**
 * PaymentWall gateway settings page
 *
 * @package Modules\Store
 * @author Supercrafter100
 * @version 2.0.2
 * @license MIT
 */
require_once(ROOT_PATH . '/modules/Store/classes/StoreConfig.php');

if (Input::exists()) {
    if (Token::check()) {
        if (isset($_POST['project_key']) && isset($_POST['secret_key']) && strlen($_POST['project_key']) && strlen($_POST['secret_key'])) {
            $settings = [];
            $settings['paymentwall/public_key'] = $_POST['project_key'];
            $settings['paymentwall/private_key'] = $_POST['secret_key'];
            $settings['paymentwall/widget_id'] = $_POST['widget_id'] ?? 'p1';
            StoreConfig::set($settings);
        }

        // Is this gateway enabled
        if (isset($_POST['enable']) && $_POST['enable'] == 'on') $enabled = 1;
        else $enabled = 0;

        DB::getInstance()->update('store_gateways', $gateway->getId(), [
            'enabled' => $enabled
        ]);

        Session::flash('gateways_success', $language->get('admin', 'successfully_updated'));
    } else
        $errors = [$language->get('general', 'invalid_token')];
}

$smarty->assign([
    'SETTINGS_TEMPLATE' => ROOT_PATH . '/modules/Store/gateways/PaymentWall/gateway_settings/settings.tpl',
    'ENABLE_VALUE' => ((isset($enabled)) ? $enabled : $gateway->isEnabled())
]);