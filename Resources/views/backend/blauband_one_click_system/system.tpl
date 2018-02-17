<div class="system-group">
    <h3 class="system-header">{$system.name} ({$system.state|snippet:$system.state:'blauband/ocs'})</h3>
        <div class="system-data">
        <div class="ui-widget">
            <div class="three-cols">
                <h4>
                    {s name="generel" namespace="blauband/ocs"}{/s}
                </h4>
                <label>{s name="name" namespace="blauband/ocs"}{/s}:</label> {$system.name}<br/>
                <label>{s name="createDate" namespace="blauband/ocs"}{/s}:</label> {$system.createDate|date_format:"%d.%m.%y %H:%M"}<br/>
                <label>{s name="type" namespace="blauband/ocs"}{/s}:</label> {$system.type|snippet:$system.type:'blauband/ocs'}<br/>
                <label>{s name="state" namespace="blauband/ocs"}{/s}:</label> {$system.state}<br/>
            </div>
            <div class="three-cols">
                <h4>
                    {s name="pathsUrls" namespace="blauband/ocs"}{/s}
                </h4>
                <label>{s name="path" namespace="blauband/ocs"}{/s}:</label> {$system.path}<br/>

                {if $system.state == 'ready'}
                    <label>{s name="url" namespace="blauband/ocs"}{/s}:</label> <a href="{$smarty.server.HTTP_ORIGIN}{$system.url}" target="_blank">{$smarty.server.HTTP_HOST}{$system.url}</a><br/>
                {/if}
            </div>
            <div class="three-cols">
                <h4>
                    {s name="settings" namespace="blauband/ocs"}{/s}
                </h4>
                <label>{s name="preventmail" namespace="blauband/ocs"}{/s}:</label> <span class="ui-icon {if $system.preventMail}ui-icon-check{else}ui-icon-close{/if}"></span><br/>
                <label>{s name="skipmedia" namespace="blauband/ocs"}{/s}:</label> <span class="ui-icon {if $system.skipMedia}ui-icon-check{else}ui-icon-close{/if}"></span><br/>
                <label>{s name="htPasswordSecure" namespace="blauband/ocs"}{/s}:</label> <span class="ui-icon {if $system.htPasswdUsername != null}ui-icon-check{else}ui-icon-close{/if}"></span><br/>
            </div>
        </div>

        {if $system.state == 'ready'}
            <div class="delete-button-wrapper">
                <button class="delete-button ui-button ui-corner-all" data-id="{$system.id}">
                    {s name="delete" namespace="blauband/ocs"}{/s}
                </button>
            </div>

            <div class="compare-article-button-wrapper">
                <button class="compare-article-button compare-button ui-button ui-corner-all" data-type="db" data-id="{$system.id}" data-group="article"  data-title="{s name="compareArticle" namespace="blauband/ocs"}{/s}">
                    {s name="compareArticle" namespace="blauband/ocs"}{/s}
                </button>

                <button class="compare-emotion-button compare-button ui-button ui-corner-all" data-type="db" data-id="{$system.id}" data-group="emotion"  data-title="{s name="compareEmotion" namespace="blauband/ocs"}{/s}">
                    {s name="compareEmotion" namespace="blauband/ocs"}{/s}
                </button>

                <button class="compare-theme-button compare-button ui-button ui-corner-all" data-type="folder" data-id="{$system.id}" data-group="theme"  data-title="{s name="compareTheme" namespace="blauband/ocs"}{/s}">
                    {s name="compareTheme" namespace="blauband/ocs"}{/s}
                </button>
            </div>
        {/if}
    </div>
</div>