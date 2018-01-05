<div class="system-group">
    <h3 class="system-header">{$system.name}</h3>
    <div class="system-data">
        <label>{s name="name" namespace="blaubandOneClickSystem"}Name{/s}:</label> {$system.name}<br/>
        <label>{s name="createDate" namespace="blaubandOneClickSystem"}Erstellungsdatum{/s}:</label> {$system.createDate}<br/>
        <label>{s name="path" namespace="blaubandOneClickSystem"}Pfad{/s}:</label> {$system.path}<br/>
        <label>{s name="type" namespace="blaubandOneClickSystem"}Typ{/s}:</label> {$system.type}<br/>
        <label>{s name="state" namespace="blaubandOneClickSystem"}Status{/s}:</label> {$system.state}<br/>

        <button class="delete-button ui-button ui-corner-all" data-id="{$system.id}">
            {s name="delete" namespace="blaubandOneClickSystem"}System entfernen{/s}
        </button>
    </div>
</div>