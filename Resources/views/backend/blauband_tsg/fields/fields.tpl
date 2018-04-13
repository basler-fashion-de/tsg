<div class="action-field {$field.type}-field" {if $field.hidden === 'true'}style="display: none"{/if}>
    {if $field.type == 'text'}
        {include file="backend/blauband_tsg/fields/text.tpl" field=$field}
    {/if}

    {if $field.type == 'checkbox'}
        {include file="backend/blauband_tsg/fields/checkbox.tpl" field=$field}
    {/if}

    {if $field.type == 'password'}
        {include file="backend/blauband_tsg/fields/password.tpl" field=$field}
    {/if}

    {if $field.type == 'select'}
        {include file="backend/blauband_tsg/fields/select.tpl" field=$field}
    {/if}
</div>