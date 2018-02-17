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
{/if}

{if $result}
    {assign 'notEmptyResult' false}

    <div id="compare">
        {foreach $result as $compare}
            {if !$compare.empty_table && $compare.state != 2}
                {assign 'notEmptyResult' true}
                {include file="backend/blauband_db_compare/compare_table.tpl" compare=$compare }
            {/if}
        {/foreach}
    </div>

    {if $notEmptyResult == 'true'}
        <div class="commit-button-wrapper">
            <button class="commit-button ui-button ui-corner-all"
                    data-url="{url controller="BlaubandDBCompare" action="commit" id=$id group=$group}">
                {s name="commitToLive" namespace="blauband/ocs"}{/s}
            </button>
        </div>
    {else}
        {s name="systemsItendical" namespace="blauband/ocs"}{/s}
    {/if}
{/if}
</body>

</html>
