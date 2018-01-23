<div id="action-field">
    <button id="create-button" class="ui-button ui-corner-all ui-widget"
            data-activeText="{s name="create" namespace="blaubandOneClickSystem"}System erstellen{/s}"
            data-disabledText="{s name="createDisabled" namespace="blaubandOneClickSystem"}Anfrage wird bearbeitet{/s}">
        {s name="create" namespace="blaubandOneClickSystem"}System erstellen{/s}
    </button>

    <a id="show-options-button" class="">
        {s name="showOptions" namespace="blaubandOneClickSystem"}Einstellungen anzeigen{/s}
    </a>

    <div id="options" style="display: none">
        <form id="options-form">
            <div class="ui-widget">
                <div class="three-cols">
                    <h4>
                        {s name="generel" namespace="blaubandOneClickSystem"}Allgemein{/s}
                    </h4>
                    <label for="name">
                        {s name="name" namespace="blaubandOneClickSystem"}Name{/s}:
                    </label>
                    <input name="name" id="name"></br>

                    <label for="type">
                        {s name="type" namespace="blaubandOneClickSystem"}Type{/s}:
                    </label>
                    <select name="type" id="type">
                        <option value="local"
                                selected="selected">{s name="typeLocal" namespace="blaubandOneClickSystem"}Lokal{/s}</option>
                        <option disabled>Amazon EC2</option>
                    </select></br>

                    <label for="preventmail">
                        {s name="preventmail" namespace="blaubandOneClickSystem"}Email Versand unterbinden{/s}:
                    </label>
                    <input type="checkbox" name="preventmail" id="preventmail"></br>

                    <label for="skipmedia">
                        {s name="skipmedia" namespace="blaubandOneClickSystem"}Medienordner nicht kopieren{/s}:
                    </label>
                    <input type="checkbox" name="skipmedia" id="skipmedia"></br>
                </div>
                <div class="three-cols">
                    <h4>
                        {s name="database" namespace="blaubandOneClickSystem"}Datenbank{/s}
                    </h4>
                    <label for="dbhost">
                        {s name="dbhost" namespace="blaubandOneClickSystem"}Datenbank Host{/s}:
                    </label>
                    <input name="dbhost" id="dbhost" value="{$dbhost}"></br>

                    <label for="dbuser">
                        {s name="dbuser" namespace="blaubandOneClickSystem"}Datenbank Benutzername{/s}:
                    </label>
                    <input name="dbuser" id="dbuser" value="{$dbuser}"></br>

                    <label for="dbpass">
                        {s name="dbpass" namespace="blaubandOneClickSystem"}Datenbank Passwort{/s}:
                    </label>
                    <input name="dbpass" type="password" id="dbpass" value="{$dbpass}"></br>

                    <label for="dbname">
                        {s name="dbname" namespace="blaubandOneClickSystem"}Datenbank Name{/s}:
                    </label>
                    <input name="dbname" id="dbname"></br>

                    <label for="dboverwrite">
                        {s name="dboverwrite" namespace="blaubandOneClickSystem"}Datenbank Überschreiben{/s}:
                    </label>
                    <input type="checkbox" name="dboverwrite" id="dboverwrite"></br>
                </div>
                <div class="three-cols">
                    <h4>
                        {s name="security" namespace="blaubandOneClickSystem"}Sicherheit{/s}
                    </h4>
                    <label for="htpasswd">
                        {s name="htpasswd" namespace="blaubandOneClickSystem"}.htpasswd erstelllen{/s}:
                    </label>
                    <input type="checkbox" name="htpasswd" id="htpasswd"></br>

                    <label for="htpasswdusername">
                        {s name="htpasswdusername" namespace="blaubandOneClickSystem"}.htpasswd Benutzername{/s}:
                    </label>
                    <input name="htpasswdusername" id="htpasswdusername"></br>

                    <label for="htpasswdpassword">
                        {s name="htpasswdpassword" namespace="blaubandOneClickSystem"}.htpasswd Passwort{/s}:
                    </label>
                    <input name="htpasswdpassword" id="htpasswdpassword" type="password"></br>

                </div>
            </div>
        </form>
    </div>
</div>