<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_common/header.tpl"}
</head>

<body>
<div
        id="one-click-system"
        data-createSystemUrl="{url action=createSystem}"
        data-deleteSystemUrl="{url action=deleteSystem}"
        data-duplicateMediaFolderUrl="{url action=duplicateMediaFolder}"
        data-systemListUrl="{url action=systemList}">

    {include file="backend/blauband_one_click_system/action_field.tpl"}

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

    <div id="system-list">
        {if !empty($systems)}
            {include file="backend/blauband_one_click_system/system_list.tpl" systems=$systems}
        {/if}
    </div>

</div>

</body>

</html>
