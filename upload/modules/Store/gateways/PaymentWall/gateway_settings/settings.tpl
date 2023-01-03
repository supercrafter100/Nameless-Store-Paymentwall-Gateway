<form action="" method="post">
    <div class="card shadow border-left-primary">
        <div class="card-body">
            <h5><i class="icon fa fa-info-circle"></i> Info</h5>
            The values of these fields are hidden for security reasons.<br />If you are updating these settings, please enter both the client ID and the client secret together.
        </div>
    </div>
    <br />
    <div class="card shadow border-left-warning">
        <div class="card-body">
            <h5><i class="icon fa fa-info-circle"></i> Important</h5>
            Make sure you set the PingBack URL to <code>{$PINGBACK_URL}</code> in the PaymentWall configuration!
        </div>
    </div>

    <br />

    <div class="form-group">
        <label for="inputProjectKey">Project key</label>
        <input class="form-control" type="text" id="inputProjectKey" name="project_key" placeholder="The values of these fields are hidden for security reasons.">
    </div>
    <div class="form-group">
        <label for="inputSecretKey">Secret Key</label>
        <input class="form-control" type="text" id="inputSecretKey" name="secret_key" placeholder="The values of these fields are hidden for security reasons.">
    </div>
    <div class="form-group">
        <label for="inputWidgetId">Widget code</label>
        <input class="form-control" type="text" id="inputWidgetId" name="widget_id" placeholder="The values of these fields are hidden for security reasons.">
    </div>

    <div class="form-group custom-control custom-switch">
        <input id="inputEnabled" name="enable" type="checkbox" class="custom-control-input"{if $ENABLE_VALUE eq 1} checked{/if} />
        <label class="custom-control-label" for="inputEnabled">Enable Payment Method</label>
    </div>

    <div class="form-group">
        <input type="hidden" name="token" value="{$TOKEN}">
        <input type="submit" value="{$SUBMIT}" class="btn btn-primary">
    </div>
</form>