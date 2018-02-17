<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_common/header.tpl"}
</head>

<body>
{if $error}
    <div class="alerts ui-widget">
        <div class="ui-state-error ui-corner-all">
            <span class="ui-icon ui-icon-alert"></span>
            <div class="content">{$error}</div>
        </div>
    </div>
{else}
    {s name="commitSuccess" namespace="blauband/ocs"}{/s}
{/if}
</body>

</html>
