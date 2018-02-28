<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_common/header.tpl"}
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

    {if $mails}
        <div class="mail-list">
            <h4>
                {s name="local_saved_mails" namespace="blauband/ocs"}{/s}
            </h4>
            {include file="backend/blauband_one_click_system/mails.tpl"}
        </div>
    {/if}

</div>
</body>
</html>
