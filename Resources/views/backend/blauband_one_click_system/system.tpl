<div class="system-group">
    <h3 class="system-header">{$system.name} ({$system.state|snippet:$system.state:'blaubandOneClickSystem'})</h3>
    <div class="system-data">
        <div class="ui-widget">
            <div class="three-cols">
                <h4>
                    {s name="generel" namespace="blaubandOneClickSystem"}Allgemein{/s}
                </h4>
                <label>{s name="name" namespace="blaubandOneClickSystem"}Name{/s}:</label> {$system.name}<br/>
                <label>{s name="createDate" namespace="blaubandOneClickSystem"}Erstellungsdatum{/s}:</label> {$system.createDate|date_format:"%d.%m.%y %H:%M"}<br/>
                <label>{s name="type" namespace="blaubandOneClickSystem"}Typ{/s}:</label> {$system.type}<br/>
                <label>{s name="state" namespace="blaubandOneClickSystem"}Status{/s}:</label> {$system.state}<br/>
            </div>
            <div class="three-cols">
                <h4>
                    {s name="paths" namespace="blaubandOneClickSystem"}Pfade / URLs{/s}
                </h4>
                <label>{s name="path" namespace="blaubandOneClickSystem"}Pfad{/s}:</label> {$system.path}<br/>
                <label>{s name="url" namespace="blaubandOneClickSystem"}URL{/s}:</label> <a href="{$smarty.server.HTTP_ORIGIN}{$system.url}" target="_blank">{$smarty.server.HTTP_HOST}{$system.url}</a><br/>
            </div>
            <div class="three-cols">
                <h4>
                    {s name="settings" namespace="blaubandOneClickSystem"}Einstellungen{/s}
                </h4>
                <label>{s name="preventmail" namespace="blaubandOneClickSystem"}Email Versand unterbinden{/s}:</label> <span class="ui-icon {if $system.preventMail}ui-icon-check{else}ui-icon-close{/if}"></span><br/>
                <label>{s name="skipmedia" namespace="blaubandOneClickSystem"}Medienordner nicht kopieren{/s}:</label> <span class="ui-icon {if $system.skipMedia}ui-icon-check{else}ui-icon-close{/if}"></span><br/>
                <label>{s name="htPasswordSecure" namespace="blaubandOneClickSystem"}.htpasswd gesichert{/s}:</label> <span class="ui-icon {if $system.htPasswdUsername != null}ui-icon-check{else}ui-icon-close{/if}"></span><br/>
            </div>
        </div>

        {if $system.state == 'ready'}
            <div class="delete-button-wrapper">
                <button class="delete-button ui-button ui-corner-all" data-id="{$system.id}">
                    {s name="delete" namespace="blaubandOneClickSystem"}System entfernen{/s}
                </button>
            </div>
        {/if}
    </div>
</div>