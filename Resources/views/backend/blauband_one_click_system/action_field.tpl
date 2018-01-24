<div id="action-field">
    <button id="create-button" class="ui-button ui-corner-all ui-widget"
            data-activeText="{s name="create" namespace="blauband/ocs"}{/s}"
            data-disabledText="{s name="createDisabled" namespace="blauband/ocs"}{/s}">
        {s name="create" namespace="blauband/ocs"}{/s}
    </button>

    <a id="show-options-button" class="">
        {s name="showOptions" namespace="blauband/ocs"}{/s}
    </a>

    <div id="options" style="display: none">
        <form id="options-form">
            <div class="ui-widget">
                <div class="three-cols">
                    <h4>
                        {s name="generel" namespace="blauband/ocs"}{/s}
                    </h4>
                    <label for="name">
                        {s name="name" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input name="name" id="name"></br>

                    <label for="type">
                        {s name="type" namespace="blauband/ocs"}{/s}:
                    </label>
                    <select name="type" id="type">
                        <option value="local"
                                selected="selected">{s name="typeLocal" namespace="blauband/ocs"}{/s}</option>
                        <option disabled>{s name="typeAmazon" namespace="blauband/ocs"}{/s}</option>
                    </select></br>

                    <label for="preventmail">
                        {s name="preventmail" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input type="checkbox" name="preventmail" id="preventmail"></br>

                    <label for="skipmedia">
                        {s name="skipmedia" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input type="checkbox" name="skipmedia" id="skipmedia"></br>
                </div>
                <div class="three-cols">
                    <h4>
                        {s name="database" namespace="blauband/ocs"}{/s}
                    </h4>
                    <label for="dbhost">
                        {s name="dbhost" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input name="dbhost" id="dbhost" value="{$dbhost}"></br>

                    <label for="dbuser">
                        {s name="dbuser" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input name="dbuser" id="dbuser" value="{$dbuser}"></br>

                    <label for="dbpass">
                        {s name="dbpass" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input name="dbpass" type="password" id="dbpass" value="{$dbpass}"></br>

                    <label for="dbname">
                        {s name="dbname" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input name="dbname" id="dbname"></br>

                    <label for="dboverwrite">
                        {s name="dboverwrite" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input type="checkbox" name="dboverwrite" id="dboverwrite"></br>
                </div>
                <div class="three-cols">
                    <h4>
                        {s name="security" namespace="blauband/ocs"}{/s}
                    </h4>
                    <label for="htpasswd">
                        {s name="htpasswd" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input type="checkbox" name="htpasswd" id="htpasswd"></br>

                    <label for="htpasswdusername">
                        {s name="htpasswdusername" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input name="htpasswdusername" id="htpasswdusername"></br>

                    <label for="htpasswdpassword">
                        {s name="htpasswdpassword" namespace="blauband/ocs"}{/s}:
                    </label>
                    <input name="htpasswdpassword" id="htpasswdpassword" type="password"></br>

                </div>
            </div>
        </form>
    </div>
</div>