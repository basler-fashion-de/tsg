
    {if !empty($systems)}
        <h3>{s name="existingSystems" namespace="blauband/tsg"}{/s}</h3>
        <div id="systems" class="ui-accordion">
            {foreach $systems as $system}
                {include file="backend/blauband_tsg/system.tpl" system=$system}
            {/foreach}
        </div>
    {/if}
