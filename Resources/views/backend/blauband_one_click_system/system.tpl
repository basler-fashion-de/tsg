<div class="system-group">
    <h3 class="system-header">{$system.name} ({$system.state|snippet:$system.state:'blauband/ocs'})</h3>
    <div class="system-data">
        <div class="ui-widget">
            <fieldset>
                <legend>{s name="general" namespace="blauband/ocs"}{/s}:</legend>
                <label>{s name="name" namespace="blauband/ocs"}{/s}:</label> {$system.name}<br/>
                <label>{s name="createDate" namespace="blauband/ocs"}{/s}
                    :</label> {$system.createDate|date_format:"%d.%m.%y %H:%M"}<br/>
                {*<label>{s name="type" namespace="blauband/ocs"}{/s}*}
                {*:</label> {$system.type|snippet:$system.type:'blauband/ocs'}<br/>*}
                {*<label>{s name="state" namespace="blauband/ocs"}{/s}:</label> {$system.state}<br/>*}

                <label>{s name="database" namespace="blauband/ocs"}{/s}
                    :</label> {if $system.dbHost == 'localhost'}
                    {s name="dblocal" namespace="blauband/ocs"}{/s}
                {else}
                    {s name="dbremote" namespace="blauband/ocs"}{/s}
                {/if}<br/>

                <label>{s name="mediacopied" namespace="blauband/ocs"}{/s}:</label> <span
                        class="ui-icon {if $system.mediaFolderDuplicated}ui-icon-check{else}ui-icon-close{/if}"></span><br/>


            </fieldset>

            <fieldset>
                <legend>{s name="pathsUrls" namespace="blauband/ocs"}{/s}:</legend>
                <label>{s name="path" namespace="blauband/ocs"}{/s}:</label> {$system.path}<br/>

                {if $system.state == 'ready'}
                    <label>{s name="url" namespace="blauband/ocs"}{/s}:</label>
                    <a href="{$smarty.server.HTTP_ORIGIN}{$system.url}"
                       target="_blank">{$smarty.server.HTTP_HOST}{$system.url}</a>
                    <br/>
                    <label>{s name="backendurl" namespace="blauband/ocs"}{/s}:</label>
                    <a href="{$smarty.server.HTTP_ORIGIN}{$system.url}/backend"
                       target="_blank">{$smarty.server.HTTP_HOST}{$system.url}/backend</a>
                    <br/>
                {/if}

            </fieldset>

            <fieldset>
                <legend>{s name="startsettings" namespace="blauband/ocs"}{/s}:</legend>
                {assign startParams unserialize($system.startParameter)}
                
                <label>{s name="editmodeactiveted" namespace="blauband/ocs"}{/s}:</label> <span
                        class="ui-icon {if $startParams.editMode}ui-icon-check{else}ui-icon-close{/if}"></span><br/>

                <label>{s name="servicemodeactiveted" namespace="blauband/ocs"}{/s}:</label> <span
                        class="ui-icon {if $startParams.serviceMode}ui-icon-check{else}ui-icon-close{/if}"></span><br/>

                <label>{s name="mailprevented" namespace="blauband/ocs"}{/s}:</label> <span
                        class="ui-icon {if $startParams.preventMail}ui-icon-check{else}ui-icon-close{/if}"></span><br/>

                <label>{s name="googleindexprevented" namespace="blauband/ocs"}{/s}:</label> <span
                        class="ui-icon {if $startParams.preventGoogleIndex}ui-icon-check{else}ui-icon-close{/if}"></span><br/>


                <label>{s name="htPasswordSecure" namespace="blauband/ocs"}{/s}:</label> <span
                        class="ui-icon {if !empty($system.htPasswdUsername) && !empty($system.htPasswdPassword)}ui-icon-check{else}ui-icon-close{/if}"></span><br/>

                {if !empty($system.htPasswdUsername) && !empty($system.htPasswdPassword)}
                    <label>{s name="htpasswdusername" namespace="blauband/ocs"}{/s}:</label>
                    {$system.htPasswdUsername}
                    <br/>
                    <label>{s name="htpasswdpassword" namespace="blauband/ocs"}{/s}:</label>
                    {$system.htPasswdPassword}
                    <br/>
                {/if}
            </fieldset>
        </div>


        {if $system.state == 'ready'}
            <fieldset>
                <legend>{s name="options" namespace="blauband/ocs"}{/s}:</legend>
                <div class="delete-button-wrapper">
                    <button class="delete-button ui-button ui-corner-all" data-id="{$system.id}">
                        {s name="delete" namespace="blauband/ocs"}{/s}
                    </button>
                </div>
                {if !$system.mediaFolderDuplicated}
                    <div class="option-button-wrapper">
                        <button class="media-button ui-button ui-corner-all" data-id="{$system.id}">
                            {s name="duplicateMedia" namespace="blauband/ocs"}{/s}
                        </button>
                    </div>
                {/if}
            </fieldset>
            <fieldset>
                <legend>{s name="compareTitle" namespace="blauband/ocs"}{/s}:</legend>
                <div class="compare-button-wrapper">
                    <button class="compare-article-button compare-button ui-button ui-corner-all" data-id="{$system.id}"
                            data-group="article" data-title="{s name="compareArticle" namespace="blauband/ocs"}{/s}">
                        {s name="compareArticle" namespace="blauband/ocs"}{/s}
                    </button>

                    <button class="compare-emotion-button compare-button ui-button ui-corner-all" data-id="{$system.id}"
                            data-group="emotion" data-title="{s name="compareEmotion" namespace="blauband/ocs"}{/s}">
                        {s name="compareEmotion" namespace="blauband/ocs"}{/s}
                    </button>

                    <button class="compare-snippets-button compare-button ui-button ui-corner-all"
                            data-id="{$system.id}"
                            data-group="snippets" data-title="{s name="compareSnippets" namespace="blauband/ocs"}{/s}">
                        {s name="compareSnippets" namespace="blauband/ocs"}{/s}
                    </button>

                    <button class="compare-theme-button compare-button ui-button ui-corner-all" data-id="{$system.id}"
                            data-group="theme" data-title="{s name="compareTheme" namespace="blauband/ocs"}{/s}">
                        {s name="compareTheme" namespace="blauband/ocs"}{/s}
                    </button>

                    <button class="compare-plugin-button compare-button ui-button ui-corner-all" data-id="{$system.id}"
                            data-group="plugins" data-title="{s name="comparePlugin" namespace="blauband/ocs"}{/s}">
                        {s name="comparePlugin" namespace="blauband/ocs"}{/s}
                    </button>
                </div>
            </fieldset>
        {/if}
    </div>
</div>