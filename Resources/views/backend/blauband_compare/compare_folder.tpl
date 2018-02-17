<div class="file_compare">
    <table class="title">
        <tr>
            <td>{$compare.left.path}</td>
            <td>{$compare.right.path}</td>
        </tr>

        {if $compare.maxSize}
            <tr>
                <td colspan="2">
                    {s name="maxFileSize" namespace="blauband/ocs"}{/s}
                </td>
            </tr>
        {/if}
    </table>

    {$compare.html}
</div>
