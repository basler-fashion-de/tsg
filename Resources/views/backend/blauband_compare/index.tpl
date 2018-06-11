<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_common/header.tpl"}
    <script type="text/javascript" src="{link file="backend/_public/src/js/compare.js"}"></script>
</head>

<body>
<div id="tsg"
     data-maxCount="{$maxCount}"
     data-loadUrl="{url action=loadCompare id=$id group=$group}">

    <div class="alerts ui-widget">
            <div class="ui-state-error ui-corner-all">
                <span class="ui-icon ui-icon-alert"></span>
                <div class="content">{$error}</div>
            </div>

    </div>

    <div class="alerts ui-widget shown loading-info-box">
        <div class="ui-state-highlight ui-corner-all">
            <span class="ui-icon ui-icon-notice"></span>
            <div class="content">
                {s name="loadingComapre" namespace="blauband/tsg"}{/s}
                <span class="current-counter">0</span>/<span>{$maxCount}</span>
            </div>
        </div>
    </div>

    <div id="compare">
        <h3 style="display: none; overflow: hidden" class="identical-title">{s name="systemsItendical" namespace="blauband/tsg"}{/s}</h3>

        <h3 style="display: none" class="db-title">{s name="dbChanges" namespace="blauband/tsg"}{/s}</h3>
        <div class="db-results">

        </div>

        <h3 style="display: none" class="folder-title">{s name="folderChanges" namespace="blauband/tsg"}{/s}</h3>
        <div class="folder-results">

        </div>
    </div>


    {if $commit}
        <div class="commit-button-wrapper">
            <button class="commit-button ui-button ui-corner-all"
            data-url="{url controller="BlaubandCompare" action="commit" id=$id group=$group}" disabled>
            {s name="commitToLive" namespace="blauband/tsg"}{/s}
            </button>
        </div>
    {/if}
</div>
</body>

</html>
