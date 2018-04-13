<div class="file_compare">
    <div class="title">
        {$compare.title}
    </div>

    {if $compare.maxSize}
        <div class="diff">
            {s name="maxFileSize" namespace="blauband/tsg"}{/s}
        </div>
    {/if}

    {$compare.html}
</div>
