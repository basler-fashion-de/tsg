<?php

namespace BlaubandTSG\Services\System\Local;

use BlaubandTSG\Models\System;
use Doctrine\DBAL\Connection;
use BlaubandTSG\Exceptions\SystemFileSystemException;
use Shopware\Components\Logger;

class SetUpSystemService
{
    /** @var Connection */
    private $shopConnection;

    /** @var \Enlight_Components_Snippet_Manager */
    private $snippets;

    /** @var Logger */
    private $pluginLogger;

    /** @var \Shopware_Components_Config */
    private $config;

    public function __construct(
        Connection $connection,
        \Enlight_Components_Snippet_Manager $snippets,
        Logger $pluginLogger,
        \Shopware_Components_Config $config
    )
    {
        $this->snippets = $snippets;
        $this->pluginLogger = $pluginLogger;
        $this->shopConnection = $connection;
        $this->config = $config;
    }

    public function getDefaultTestShopName()
    {
        try {
            $shopName = $this->shopConnection->fetchColumn('SELECT title FROM s_core_shops WHERE `default` = 1');
            return uniqid('Test-' . $shopName . '-', false);
        } catch (\Exception $e) {
            $this->pluginLogger->addError($e->getMessage());
            throw $e;
        }
    }

    public function changeShopTitle(Connection $guestConnection, System $system, $seperator = " - ")
    {
        try {
            $prefix = $system->getName();
            $guestConnection->exec("UPDATE s_core_shops SET title = CONCAT('$prefix','$seperator', title)");
        } catch (\Exception $e) {
            $this->pluginLogger->addError($e->getMessage());
            throw $e;
        }
    }

    public function changeShopUrl(Connection $guestConnection, System $system)
    {
        try {
            $urlPostfix = $system->getUrl();
            $guestConnection->exec("UPDATE s_core_shops SET base_path = CONCAT(IFNULL(base_path,''),'$urlPostfix')");

            /* Bei der URL muss es davor stehen. Ansonsten klappen die mehrsprachigen Shops nicht */
            $guestConnection->exec("UPDATE s_core_shops SET base_url = CONCAT('$urlPostfix', IFNULL(base_url,''))");

            if (version_compare($this->config->get('version'), '5.4.0', '<')) {
                $guestConnection->exec("UPDATE s_core_shops SET secure_base_path = CONCAT(IFNULL(secure_base_path,''),'$urlPostfix')");
            }
        } catch (\Exception $e) {
            $this->pluginLogger->addError($e->getMessage());
            throw $e;
        }
    }

    public function setShopOffline(Connection $guestConnection, System $system)
    {
        try {
            if ($system->getStartParameter()['serviceMode']) {
                $elementId = $guestConnection->fetchColumn("SELECT id FROM s_core_config_elements WHERE name = 'setoffline'");
                $defaultShop = $guestConnection->fetchColumn("SELECT id FROM s_core_shops WHERE `default` = 1");

                $guestConnection->delete('s_core_config_values', ['element_id' => $elementId]);
                $guestConnection->insert('s_core_config_values', ['element_id' => $elementId, 'shop_id' => $defaultShop, 'value' => 'b:1;']);
            }
        } catch (\Exception $e) {
            $this->pluginLogger->addError($e->getMessage());
            throw $e;
        }
    }

    public function setShopMode(Connection $guestConnection, System $system)
    {
        try {
            $guestConnection->exec("UPDATE s_core_plugins SET active = 0 WHERE name = 'HttpCache'");
        } catch (\Exception $e) {
            $this->pluginLogger->addError($e->getMessage());
            throw $e;
        }
    }

    public function setUpConfigPhp(Connection $guestConnection, System $system)
    {
        $this->pluginLogger->addError('(' . __METHOD__ . ')');

        try {
            //config.php ändern
            $configPath = $system->getPath() . "/config.php";
            $config = include $configPath;

            $config['db']['host'] = $guestConnection->getHost();
            $config['db']['port'] = $guestConnection->getPort();
            $config['db']['username'] = $guestConnection->getUsername();
            $config['db']['password'] = $guestConnection->getPassword();
            $config['db']['dbname'] = $guestConnection->getDatabase();
            $config['blauband']['tsg']['isGuest'] = true;
            $config['blauband']['tsg']['noIndex'] = $system->getStartParameter()['preventGoogleIndex'];

            $configData = "<?php\n\n return " . var_export($config, true) . ";";

            $result = file_put_contents($configPath, $configData);

            if ($result === false) {
                throw new SystemFileSystemException(
                    $this->snippets->getNamespace('blauband/tsg')->get('canNoWriteConfigPhp')
                );
            }
        } catch (\Exception $e) {
            $this->pluginLogger->addError($e->getMessage());
            throw $e;
        }
    }
}