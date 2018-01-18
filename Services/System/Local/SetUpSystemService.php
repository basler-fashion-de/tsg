<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use BlaubandOneClickSystem\Models\System;
use Doctrine\DBAL\Connection;
use BlaubandOneClickSystem\Exceptions\SystemDBException;

class SetUpSystemService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    public function changeShopTitle(Connection $guestConnection, System $system, $seperator = " - "){
        $prefix = $system->getName();
        $guestConnection->exec("UPDATE s_core_shops SET title = CONCAT('$prefix','$seperator', title)");
    }

    public function changeShopUrl(Connection $guestConnection, System $system){
        $urlPostfix = $system->getUrl();
        $guestConnection->exec("UPDATE s_core_shops SET base_path = CONCAT(IFNULL(base_path,''),'$urlPostfix')");
        $guestConnection->exec("UPDATE s_core_shops SET base_url = CONCAT(IFNULL(base_url,''),'$urlPostfix')");
        $guestConnection->exec("UPDATE s_core_shops SET secure_base_path = CONCAT(IFNULL(secure_base_path,''),'$urlPostfix')");
    }

    public function setUpConfigPhp(Connection $guestConnection, System $system){
        //config.php ändern
        $configPath = $system->getPath() . "/config.php";
        $config = include $configPath;
        $config['db']['host'] = $guestConnection->getHost();
        $config['db']['username'] = $guestConnection->getUsername();
        $config['db']['password'] = $guestConnection->getPassword();
        $config['db']['dbname'] = $guestConnection->getDatabase();
        $config['blauband']['ocs']['isGuest'] = true;

        file_put_contents($configPath, "<?php\n\n return " . var_export($config, true) . ";");
    }
}