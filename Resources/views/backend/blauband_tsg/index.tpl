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

    {include file="backend/blauband_tsg/error.tpl"}

    <h2 class="header-title">
        {s name="headerTitle" namespace="blauband/tsg"}{/s}
    </h2>

    <div class="start-button-field">
        <button id="create-button" class="start-button">
            <div>
                <h1>
                    {s name="createButton" namespace="blauband/tsg"}{/s}
                </h1>
            </div>
        </button>

        <button id="show-options-button" class="start-button last">
            <div>
                <h1>
                    {s name="showOptions" namespace="blauband/tsg"}{/s}
                </h1>
            </div>
        </button>
    </div>

    <div class="content-field">
        <div id="action-field">
            {include file="backend/blauband_tsg/action_field.tpl"}
        </div>

        <div id="system-list">
            {include file="backend/blauband_tsg/system_list.tpl" systems=$systems}
        </div>
    </div>
</div>

</body>

</html>
