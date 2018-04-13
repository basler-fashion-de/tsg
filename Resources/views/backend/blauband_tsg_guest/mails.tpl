{foreach $mails as $mail}
    <div class="mail">
        <label>{s name="mailFrom" namespace="blauband/tsg"}{/s}:</label> {$mail.from}<br/>
        {foreach $mail.to as $t}
            <label>{s name="mailTo" namespace="blauband/tsg"}{/s}:</label> {$t}<br/>
        {/foreach}
        <label>{s name="mailSubject" namespace="blauband/tsg"}{/s}:</label> {$mail.subject}<br/>
        <label>{s name="mailBody" namespace="blauband/tsg"}{/s}</label> <br/>{$mail.body}<br/>
    </div>
{/foreach}