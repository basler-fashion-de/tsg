{if $field.type == 'text'}
    {include file="backend/blauband_one_click_system/fields/text.tpl" field=$field}
{/if}

{if $field.type == 'checkbox'}
    {include file="backend/blauband_one_click_system/fields/checkbox.tpl" field=$field}
{/if}

{if $field.type == 'password'}
    {include file="backend/blauband_one_click_system/fields/password.tpl" field=$field}
{/if}

{if $field.type == 'select'}
    {include file="backend/blauband_one_click_system/fields/select.tpl" field=$field}
{/if}