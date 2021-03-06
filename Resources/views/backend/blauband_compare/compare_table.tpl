{if $compare.state != 2}
    <div class="table_compare">
        <h3 class="title">{$compare.table}</h3>

        <table>
            <tr>
                <th colspan="{count($compare.diffs)}" style="width: 49%">
                    <h3>{s name="hostSystem" namespace="blauband/tsg"}{/s}<h3/>
                </th>
                <th></th>
                <th colspan="{count($compare.diffs)}" style="width: 49%">
                    <h3>{s name="testSystem" namespace="blauband/tsg"}{/s}</h3>
                </th>
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
                                <td class="{if $d.state == 1}green{/if}{if $d.state == 3 && $d.left.$key !== $d.right.$key}yellow{/if}">
                                    {if $d.left.$key === NULL}
                                        NULL
                                    {else}
                                        {$d.left.$key|escape:"htmlall"}
                                    {/if}
                                </td>
                            {else}
                                <td class="red"></td>
                            {/if}
                        {/foreach}

                        <td class="separator"></td>

                        {foreach $compare.diffs as $key}
                            {if $d.right}
                                <td class="{if $d.state == 1}green{/if}{if $d.state == 3 && $d.left.$key !== $d.right.$key}yellow{/if}">
                                    {if $d.right.$key === NULL}
                                        NULL
                                    {else}
                                        {$d.right.$key|escape:"htmlall"}
                                    {/if}
                                </td>
                            {else}
                                <td class="red"></td>
                            {/if}
                        {/foreach}
                    </tr>
                {/if}
            {/foreach}
        </table>
    </div>
{/if}