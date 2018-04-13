{foreach $mails as $mail}
    <div class="mail">
        {assign allTo implode(', ', $mail.to)}
        <h3 class="title">{$mail.subject} - {$allTo|escape:"html"}</h3>

        <div>
            <label>{s name="mailFrom" namespace="blauband/tsg"}{/s}:</label> {$mail.from|escape:"html"}<br/>
            {foreach $mail.to as $t}
                <label>{s name="mailTo" namespace="blauband/tsg"}{/s}:</label>
                {$t|escape:"html"}
                <br/>
            {/foreach}
            <label>{s name="mailSubject" namespace="blauband/tsg"}{/s}:</label> {$mail.subject|escape:"html"}<br/>
            <label>{s name="mailBody" namespace="blauband/tsg"}{/s}</label> <br/>{$mail.body}
        </div>

    </div>
{/foreach}