<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_one_click_system/header.tpl"}
</head>

<body>
<div id="one-click-system">
    <div>
        <div class="alerts ui-widget">
            <div class="ui-state-error ui-corner-all">
                <span class="ui-icon ui-icon-alert"></span>
                <div class="content">
                    {s name="guest_system_error" namespace="blaubandOneClickSystem"}Sie befinden sich bereits in einem Gast-System. Um weitere System zu erstellen müssen Sie dieses im Host-System machen.{/s}
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
