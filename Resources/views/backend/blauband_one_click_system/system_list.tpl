<h3>{s name="existingSystems" namespace="blaubandOneClickSystem"}Bereits erstellte Systeme{/s}</h3>
<div id="systems" class="ui-accordion">
    {foreach $systems as $system}
        {include file="backend/blauband_one_click_system/system.tpl" system=$system}
    {/foreach}
</div>