{block name="backend/base/header/favicon"}
    {if $blaubandOcsIsGuest == 'true'}
        <link rel="icon"
              href="data:image/png;base64,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"
              type="image/x-icon"/>
        <link rel="shortcut icon"
              href="data:image/png;base64,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"
              type="image/x-icon"/>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}