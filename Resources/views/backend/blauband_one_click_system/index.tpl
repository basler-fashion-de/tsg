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

    <h2 class="header-title">
        {s name="headerTitle" namespace="blauband/ocs"}{/s}
    </h2>

    <div class="start-button-field">
        <button id="create-button" class="start-button"
                data-activeText="{s name="createButton" namespace="blauband/ocs"}{/s}"
                data-disabledText="{s name="createDisabled" namespace="blauband/ocs"}{/s}">
            <div>
                <h1>
                    {s name="createButton" namespace="blauband/ocs"}{/s}
                </h1>
            </div>
        </button>

        <button id="show-options-button" class="start-button last">
            <div>
                <h1>
                    {s name="showOptions" namespace="blauband/ocs"}{/s}
                </h1>
            </div>
        </button>
    </div>

    <div class="content-field">
        {include file="backend/blauband_one_click_system/error.tpl"}

        <div id="action-field">
            {include file="backend/blauband_one_click_system/action_field.tpl"}
        </div>

        <div id="system-list">
            {include file="backend/blauband_one_click_system/system_list.tpl" systems=$systems}
        </div>
    </div>
</div>

</body>

</html>
