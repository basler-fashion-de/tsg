{include file="backend/blauband_one_click_system/fields/label.tpl" field=$field}

<input type="checkbox"
       name="{$field['title']}"
       id="{$field['title']}"
       {if $field['class']}class="{$field['class']}"{/if}
        {if $field['info']}title="{''|snippet:$field['info']:'blauband/ocs'}"{/if}
        {if $field['default'] == 'true'}checked{/if}
/></br>