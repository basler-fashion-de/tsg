<div id="action-field">
    <button id="create-button" class="ui-button ui-corner-all ui-widget">
        {s name="create" namespace="blaubandOneClickSystem"}System erstellen{/s}
    </button>

    <a id="show-options-button" class="">
        {s name="showOptions" namespace="blaubandOneClickSystem"}Einstellungen anzeigen{/s}
    </a>

    <div id="options" style="display: none">
        <form id="options-form">
            <div class="ui-widget">
                <div class="two-cols">
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
                    </select>
                </div>
                <div class="two-cols">
                    <label for="dbhost">
                        {s name="dbhost" namespace="blaubandOneClickSystem"}Datenbank Host{/s}:
                    </label>
                    <input name="dbhost" id="dbhost" value="{$dbhost}"></br>

                    <label for="dbuser">
                        {s name="dbuser" namespace="blaubandOneClickSystem"}Datenbank User{/s}:
                    </label>
                    <input name="dbuser" id="dbuser" value="{$dbuser}"></br>

                    <label for="dbpass">
                        {s name="dbpass" namespace="blaubandOneClickSystem"}Datenbank Password{/s}:
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

            </div>
        </form>
    </div>
</div>