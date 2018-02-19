<div class="table_compare">
    <h3 class="title">{$compare.table}</h3>

    <table>
        <tr style="height: 1px">
            <th colspan="{count($compare.diffs)}" style="width: 49%"></th>
            <th></th>
            <th colspan="{count($compare.diffs)}" style="width: 49%"></th>
        </tr>
        <tr>
            {foreach $compare.diffs as $head}
                <th>{$head}</th>
            {/foreach}

            <th class="separator"></th>

            {foreach $compare.diffs as $head}
                <th>{$head}</th>
            {/foreach}
        </tr>

        {foreach $compare.data as $d}
            {if $d.state != 2}
                <tr>
                    {foreach $compare.diffs as $key}
                        {if $d.left}
                            <td class="{if $d.state == 1}green{/if}{if $d.state == 3 && $d.left.$key != $d.right.$key}yellow{/if}">{$d.left.$key}</td>
                        {else}
                            <td class="red"></td>
                        {/if}
                    {/foreach}

                    <td class="separator"></td>

                    {foreach $compare.diffs as $key}
                        {if $d.right}
                            <td class="{if $d.state == 1}green{/if}{if $d.state == 3 && $d.left.$key != $d.right.$key}yellow{/if}">{$d.right.$key}</td>
                        {else}
                            <td class="red"></td>
                        {/if}
                    {/foreach}
                </tr>
            {/if}
        {/foreach}
    </table>
</div>
