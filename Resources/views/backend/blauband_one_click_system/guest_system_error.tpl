<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_one_click_system/header.tpl"}
</head>

<body>
<div id="one-click-system">
    <div class="alerts ui-widget shown">
        <div class="ui-state-error ui-corner-all">
            <span class="ui-icon ui-icon-alert"></span>
            <div class="content">
                {s name="guest_system_error" namespace="blauband/ocs"}{/s}
            </div>
        </div>
    </div>
</div>
</body>
</html>
