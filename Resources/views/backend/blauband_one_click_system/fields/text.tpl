<input
        name="{$field['title']}"
        id="{$field['title']}"
        {if $field['default']}value="{$field['default']}"{/if}
        {if $field['class']}class="{$field['class']}"{/if}
        {if $field['info']}title="{''|snippet:$field['info']:'blauband/ocs'}"{/if}
/>

{include file="backend/blauband_one_click_system/fields/label.tpl" field=$field}

</br>