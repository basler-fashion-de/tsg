{include file="backend/blauband_tsg/fields/label.tpl" field=$field}

<input
        name="{$field['title']}"
        id="{$field['title']}"
        {if $field['default']}value="{$field['default']}"{/if}
        {if $field['class']}class="{$field['class']}"{/if}
        {if $field['info']}title="{''|snippet:$field['info']:'blauband/tsg'}"{/if}
/>