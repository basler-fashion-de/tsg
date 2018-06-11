{foreach $compare as $c}
    {if $c.state != 2}
        <div class="file_compare">
            <div class="title">
                {$c.title}
            </div>

            {if $c.maxSize}
                <div class="diff">
                    {s name="maxFileSize" namespace="blauband/tsg"}{/s}
                </div>
            {/if}

            {$c.html}
        </div>
    {/if}
{/foreach}