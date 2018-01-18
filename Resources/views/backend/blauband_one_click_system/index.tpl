<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_one_click_system/header.tpl"}
</head>

<body>
<div
        id="one-click-system"
        data-createSystemUrl="{url action=createSystem}"
        data-deleteSystemUrl="{url action=deleteSystem}"
        data-systemListUrl="{url action=systemList}">

    <div class="alerts ui-widget">
        <div class="ui-state-error ui-corner-all">
            <span class="ui-icon ui-icon-alert"></span>
            <div class="content"></div>
        </div>

        <div class="ui-state-highlight ui-corner-all">
            <span class="ui-icon ui-icon-info"></span>
            <div class="content"></div>
        </div>
    </div>

    {include file="backend/blauband_one_click_system/action_field.tpl"}

    {if !empty($systems)}
        <h3>{s name="existingSystems" namespace="blaubandOneClickSystem"}Bereits erstellte Systeme{/s}</h3>
        <div id="system-list">
            {include file="backend/blauband_one_click_system/system_list.tpl" systems=$systems}
        </div>
    {/if}

</div>

</body>

</html>
