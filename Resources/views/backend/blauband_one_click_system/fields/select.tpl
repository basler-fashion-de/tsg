{include file="backend/blauband_one_click_system/fields/label.tpl" field=$field}

<select name="{$field['title']}"
        id="{$field['title']}"
        {if $field['class']}class="{$field['class']}"{/if}
        {if $field['info']}title="{''|snippet:$field['info']:'blauband/ocs'}"{/if}>
    {foreach $field['option'] as $option}
        <option value="{$option['value']}"
                {if $option['selected'] == 'true'}selected="selected"{/if}
        >
            {''|snippet:$option['snippet']:'blauband/ocs'}
        </option>
    {/foreach}
</select>
