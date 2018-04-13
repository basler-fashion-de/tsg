{include file="backend/blauband_tsg/fields/label.tpl" field=$field}

<select name="{$field['title']}"
        id="{$field['title']}"
        {if $field['class']}class="{$field['class']}"{/if}
        {if $field['info']}title="{''|snippet:$field['info']:'blauband/tsg'}"{/if}>
    {foreach $field['option'] as $option}
        <option value="{$option['value']}"
                {if $option['selected'] == 'true'}selected="selected"{/if}
        >
            {''|snippet:$option['snippet']:'blauband/tsg'}
        </option>
    {/foreach}
</select>
