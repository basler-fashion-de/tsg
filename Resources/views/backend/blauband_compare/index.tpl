<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_common/header.tpl"}
</head>

<body>
<div id="one-click-system">
    {if $error}
        <div class="alerts ui-widget shown">
            <div class="ui-state-error ui-corner-all">
                <span class="ui-icon ui-icon-alert"></span>
                <div class="content">{$error}</div>
            </div>
        </div>
    {/if}

    {if $dbResult || $folderResult}
        {assign 'notEmptyResult' false}
        <div id="compare">
            {if $dbResult}
                <h3>{s name="dbChanges" namespace="blauband/ocs"}{/s}</h3>
                {foreach $dbResult as $compare}
                    {if !$compare.empty_table && $compare.state != 2}
                        {assign 'notEmptyResult' true}
                        {include file="backend/blauband_compare/compare_table.tpl" compare=$compare }
                    {/if}
                {/foreach}
            {/if}


            {if $folderResult}
                <h3>{s name="folderChanges" namespace="blauband/ocs"}{/s}</h3>
                {foreach $folderResult as $compare}
                    {if $compare.state != 2}
                        {assign 'notEmptyResult' true}
                        {include file="backend/blauband_compare/compare_folder.tpl" compare=$compare }
                    {/if}
                {/foreach}
            {/if}
        </div>
        {if $notEmptyResult == 'true' && $commit}
            <div class="commit-button-wrapper">
                <button class="commit-button ui-button ui-corner-all"
                        data-url="{url controller="BlaubandCompare" action="commit" id=$id group=$group}">
                    {s name="commitToLive" namespace="blauband/ocs"}{/s}
                </button>
            </div>
        {else}
            {s name="systemsItendical" namespace="blauband/ocs"}{/s}
        {/if}
    {/if}
</div>
</body>

</html>
