<input type="checkbox"
       name="{$field['title']}"
       id="{$field['title']}"
       {if $field['class']}class="{$field['class']}"{/if}
        {if $field['info']}title="{''|snippet:$field['info']:'blauband/tsg'}"{/if}
        {if $field['default'] == 'true'}checked{/if}
/>

{include file="backend/blauband_tsg/fields/label.tpl" field=$field}