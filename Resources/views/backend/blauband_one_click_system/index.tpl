<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/redmond/jquery-ui.min.css"></link>

    <script>{include file="{$publicFilePath}src/js/autocomplete-system-names.js"}</script>
    <script>{include file="{$publicFilePath}src/js/oneclicksystem.js"}</script>
    <style>{include file="{$publicFilePath}src/css/all.css"}</style>
</head>

<body>
<div
        id="one-click-system"
        data-createSystemUrl="{url modul=backend controller=blaubandOneClickSystem action=createSystem}">

    <div id="action-field">
        <button id="create-button" class="ui-button ui-corner-all ui-widget">
            {s name="create" namespace="blaubandOneClickSystem"}System erstellen{/s}
        </button>

        <a id="show-options-button" class="ui-icon ui-icon-circle-triangle-s">
            {s name="showOptions" namespace="blaubandOneClickSystem"}Einstellungen anzeigen{/s}
        </a>

        <div id="options" style="display: none">
            <div class="ui-widget">
                <label for="name">Name: </label>
                <input id="name">
            </div>
        </div>
    </div>

    <h3>{s name="existingSystems" namespace="blaubandOneClickSystem"}Bereits erstellte Systeme{/s}</h3>
    <div id="system-list">
        {include file="backend/blauband_one_click_system/system_list.tpl" systems=$systems}
    </div>
</div>

</body>

</html>
