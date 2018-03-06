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
                {s name="guest_system_error" namespace="blauband/ocs"}{/s}
            </div>
        </div>
    </div>

    <fieldset>
        <legend>{s name="allow_send_mail" namespace="blauband/ocs"}{/s}: </legend>
        <label for="radio-yes">{s name="yes" namespace="blauband/ocs"}{/s}</label>
        <input type="radio" name="radio-mail" id="radio-yes" {if $mailsAllow}checked{/if}>
        <label for="radio-no">{s name="no" namespace="blauband/ocs"}{/s}</label>
        <input type="radio" name="radio-mail" id="radio-no" {if !$mailsAllow}checked{/if}>
    </fieldset>

    {if $mails}
        <div class="mail-list">
            <h4>
                {s name="local_saved_mails" namespace="blauband/ocs"}{/s}
            </h4>


            in eine accordion packen


            {include file="backend/blauband_one_click_system_guest/mails.tpl"}
        </div>
    {/if}

</div>
</body>
</html>
