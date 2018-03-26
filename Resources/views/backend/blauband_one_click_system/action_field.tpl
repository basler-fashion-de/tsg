<div id="options">
    <form id="options-form">
        <h4>
            {s name="showOptions" namespace="blauband/ocs"}{/s}
        </h4>

        <div class="ui-widget">
            {for $i = 1; $i <= 3; $i++}
                <div>
                    {foreach $actionFields as $group}
                        {if $group['column'] == $i}
                            <div class="action-field-group group-{$group['snippet']}"
                                 {if $group['hidden'] == 'true'}style="display: none"{/if}>

                                {if !empty($group['snippet'])}
                                    <h4>
                                        {''|snippet:$group['snippet']:'blauband/ocs'}
                                    </h4>
                                {/if}

                                {if isset($group['parameters'][0])}
                                    {foreach $group['parameters'] as $parameter}
                                        {include file="backend/blauband_one_click_system/fields/fields.tpl" field=$parameter}
                                    {/foreach}
                                {else}
                                    {include file="backend/blauband_one_click_system/fields/fields.tpl" field=$group['parameters']}
                                {/if}
                            </div>
                        {/if}
                    {/foreach}
                </div>
            {/for}
        </div>
    </form>

    <div class="button-right-wrapper full-width">
        <button id="back-button" class="ui-button ui-corner-all ui-widget">
            {s name="back" namespace="blauband/ocs"}{/s}
        </button>

        <button id="next-button" class="ui-button ui-corner-all ui-widget">
            {s name="next" namespace="blauband/ocs"}{/s}
        </button>
    </div>
</div>
