<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
            integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/redmond/jquery-ui.min.css"></link>

    <script>{include file="{$publicFilePath}src/js/autocomplete-system-names.js"}</script>
    <script>{include file="{$publicFilePath}src/js/oneclicksystem.js"}</script>
    <style>{include file="{$publicFilePath}src/css/all.css"}</style>
</head>

<body>
<div
        id="one-click-system"
        data-createSystemUrl="{url action=createSystem}"
        data-deleteSystemUrl="{url action=deleteSystem}">

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
