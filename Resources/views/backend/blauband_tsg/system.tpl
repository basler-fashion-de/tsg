<div class="system-group">
    {assign percent $system.state|cat:"_percent"}
    <h3 class="system-header">
        {$system.name} ({$system.state|snippet:$system.state:'blauband/tsg'})

        {if {''|snippet:$percent:'blauband/tsg'} != 100}
            <progress max="100" value="{''|snippet:$percent:'blauband/tsg'}"></progress>
        {/if}
    </h3>
    <div class="system-data">
        <div class="ui-widget">
            {if $system.state == 'ready'}
                <div class="button-right-wrapper delete-button-wrapper">
                    <button class="delete-button red ui-button ui-corner-all" data-id="{$system.id}">
                        {s name="delete" namespace="blauband/tsg"}{/s}
                    </button>
                </div>
            {/if}

            <label>{s name="url" namespace="blauband/tsg"}{/s}:</label>
            <a href="{$smarty.server.HTTP_ORIGIN}{$system.url}"
               target="_blank">{$smarty.server.HTTP_HOST}{$system.url}</a>
            <br/>
            <label>{s name="backendurl" namespace="blauband/tsg"}{/s}:</label>
            <a href="{$smarty.server.HTTP_ORIGIN}{$system.url}/backend"
               target="_blank">{$smarty.server.HTTP_HOST}{$system.url}/backend</a>
            <br/>

            {if !empty($system.htPasswdUsername) && !empty($system.htPasswdPassword)}
                <label>{s name="htpasswdusername" namespace="blauband/tsg"}{/s}:</label>
                <div>{$system.htPasswdUsername}</div>
                <label>{s name="htpasswdpassword" namespace="blauband/tsg"}{/s}:</label>
                <div>{$system.htPasswdPassword}</div>
            {/if}

            {if $system.state == 'ready'}
                <br/>
                <br/>
                <label style="width: auto; padding-top: 8px">{s name="compareTitle" namespace="blauband/tsg"}{/s}
                    :</label>
                <div class="compare-button-wrapper">
                    <button class="compare-article-button compare-button ui-button ui-corner-all" data-id="{$system.id}"
                            data-group="article" data-title="{s name="compareArticle" namespace="blauband/tsg"}{/s}">
                        {s name="compareArticle" namespace="blauband/tsg"}{/s}
                    </button>

                    <button class="compare-emotion-button compare-button ui-button ui-corner-all" data-id="{$system.id}"
                            data-group="emotion" data-title="{s name="compareEmotion" namespace="blauband/tsg"}{/s}">
                        {s name="compareEmotion" namespace="blauband/tsg"}{/s}
                    </button>

                    <button class="compare-snippets-button compare-button ui-button ui-corner-all"
                            data-id="{$system.id}"
                            data-group="snippets" data-title="{s name="compareSnippets" namespace="blauband/tsg"}{/s}">
                        {s name="compareSnippets" namespace="blauband/tsg"}{/s}
                    </button>

                    <button class="compare-theme-button compare-button ui-button ui-corner-all" data-id="{$system.id}"
                            data-group="theme" data-title="{s name="compareTheme" namespace="blauband/tsg"}{/s}">
                        {s name="compareTheme" namespace="blauband/tsg"}{/s}
                    </button>

                    {*<button class="compare-plugin-button compare-button ui-button ui-corner-all" data-id="{$system.id}"*}
                            {*data-group="plugins" data-title="{s name="comparePlugin" namespace="blauband/tsg"}{/s}">*}
                        {*{s name="comparePlugin" namespace="blauband/tsg"}{/s}*}
                    {*</button>*}
                </div>
            {/if}

            <br/>
            <br/>
            <label>{s name="name" namespace="blauband/tsg"}{/s}:</label>
            <div>{$system.name}</div>

            <label>{s name="createDate" namespace="blauband/tsg"}{/s}:</label>
            <div>{$system.createDate|date_format:"%d.%m.%y %H:%M"}</div>


            <br/>
            <br/>
            <label class="full-width">{s name="startsettings" namespace="blauband/tsg"}{/s}:</label><br/>

            {assign startParams unserialize($system.startParameter)}
            <span class="ui-icon {if $startParams.editMode}ui-icon-check{else}ui-icon-close{/if}"></span>
            <label style="width: 50%">{s name="editmodeactiveted" namespace="blauband/tsg"}{/s}</label><br/>

            {*<span class="ui-icon {if $startParams.serviceMode}ui-icon-check{else}ui-icon-close{/if}"></span>*}
            {*<label style="width: 50%">{s name="servicemodeactiveted" namespace="blauband/tsg"}{/s}</label> <br/>*}

            <span class="ui-icon {if $startParams.preventMail}ui-icon-check{else}ui-icon-close{/if}"></span>
            <label style="width: 50%">{s name="mailprevented" namespace="blauband/tsg"}{/s}</label> <br/>

            <span class="ui-icon {if $startParams.preventGoogleIndex}ui-icon-check{else}ui-icon-close{/if}"></span>
            <label style="width: 50%">{s name="googleindexprevented" namespace="blauband/tsg"}{/s}</label> <br/>

            <span class="ui-icon {if !empty($system.htPasswdUsername) && !empty($system.htPasswdPassword)}ui-icon-check{else}ui-icon-close{/if}"></span>
            <label style="width: 50%">{s name="htPasswordSecure" namespace="blauband/tsg"}{/s}</label> <br/>

            {if $system.state == 'ready' && !$system.mediaFolderDuplicated}
                <div class="media-button-wrapper">
                    <button class="media-button ui-button ui-corner-all" data-id="{$system.id}">
                        {s name="duplicateMedia" namespace="blauband/tsg"}{/s}
                    </button>
                </div>
            {else}
                <span class="ui-icon ui-icon-check"></span>
                <label style="width: 50%">{s name="mediacopied" namespace="blauband/tsg"}{/s}</label> <br/>
            {/if}
        </div>
    </div>
</div>