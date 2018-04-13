<!DOCTYPE html>
<html>
<head>
    {include file="backend/blauband_common/header.tpl"}
</head>

<body>
<div id="one-click-system"
     data-mailUrl="{url action=allowMail}"
>
    <div class="alerts ui-widget shown">
        <div class="ui-state-error ui-corner-all">
            <span class="ui-icon ui-icon-alert"></span>
            <div class="content">
                {s name="guest_system_error" namespace="blauband/tsg"}{/s}
            </div>
        </div>
    </div>

    <fieldset>
        <legend>{s name="allow_send_mail" namespace="blauband/tsg"}{/s}: </legend>
        <label for="radio-yes">{s name="label_yes" namespace="blauband/tsg"}{/s}</label>
        <input type="radio" name="radio-mail" id="radio-yes" {if $mailsAllow}checked{/if}>
        <label for="radio-no">{s name="label_no" namespace="blauband/tsg"}{/s}</label>
        <input type="radio" name="radio-mail" id="radio-no" {if !$mailsAllow}checked{/if}>
    </fieldset>

    {if $mails}
        <div class="mail-list">
            <h4>
                {s name="local_saved_mails" namespace="blauband/tsg"}{/s}
            </h4>


            in eine accordion packen


            {include file="backend/blauband_tsg_guest/mails.tpl"}
        </div>
    {/if}

</div>
</body>
</html>
