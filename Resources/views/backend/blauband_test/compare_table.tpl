{if $compare.empty_table}
    {*Leere Tabelle*}
{elseif $compare.state == 2}
    {*Tabelle identisch*}
{else}
    <div class="title">{$compare.table}</div>

    <table>
        {foreach $compare.diffs as $head}
            <th>{$head}</th>
        {/foreach}

        <th>|||</th>

        {foreach $compare.diffs as $head}
            <th>{$head}</th>
        {/foreach}


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

                    <td>|||</td>

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
{/if}