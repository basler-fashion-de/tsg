<!DOCTYPE html>
<html>
<head>
    <style>

        .two-colmn {
            width: 50%;
            float: left;
        }

        .title {
            font-size: large;
            font-weight: bolder;
        }

        .td {
            display: inline;
            max-width: 30px;
            float: left;
            margin-left: 5px;
            height: 20px;
            overflow: hidden;
        }

        .green {
            background-color: greenyellow;
        }

        .yellow {
            background-color: yellow;
        }

        .red {
            background-color: lightpink;
        }

    </style>
</head>

<body>
Compare

{if $compare}
    {include file="backend/blauband_test/compare_table.tpl" compare=$compare }
{/if}

{if $group}
    {foreach $group as $compare}

        {include file="backend/blauband_test/compare_table.tpl" compare=$compare }
    {/foreach}
{/if}



{if $group}

{/if}

</body>
</html>
