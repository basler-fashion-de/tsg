<div id="systems" class="ui-accordion">
    {foreach $systems as $system}
        {include file="backend/blauband_one_click_system/system.tpl" system=$system}
    {/foreach}
</div>