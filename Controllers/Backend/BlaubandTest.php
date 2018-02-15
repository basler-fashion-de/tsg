<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandOneClickSystem\Services\System\Common\DBConnectionService;
use BlaubandOneClickSystem\Models\System;
use BlaubandOneClickSystem\Services\DBCompare\DBCompareResult;
use BlaubandOneClickSystem\Services\DBCompare\DBCompareGroupResult;
use BlaubandOneClickSystem\Services\ConfigService;


class Shopware_Controllers_Backend_BlaubandTest extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        return [
            'index'
        ];
    }

    /**
     * Pre dispatch method
     */
    public function preDispatch()
    {
        $pluginPath = $this->container->getParameter('blauband_one_click_system.plugin_dir');
        $this->View()->addTemplateDir($pluginPath . '/Resources/views/');
    }

    /**
     * Post dispatch method
     */
    public function postDispatch()
    {
        $pluginPath = $this->container->getParameter('blauband_one_click_system.plugin_dir');
        $this->View()->assign('publicFilePath', $pluginPath . '/Resources/views/backend/_public/');
    }

    /**
     * Startseite
     */
    public function indexAction()
    {
        //$this->Front()->Plugins()->ViewRenderer()->setNoRender();

        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');

        $systemList = $modelManager->getRepository(System::class)->findAll();
        /** @var System $system */
        $system = $systemList[0];

        if(empty($system)){
            die('Kein System angelegt');
        }
        //$systemListArray = $modelManager->toArray($systemList);

        /** @var DBConnectionService $connectionService */
        $connectionService = $this->container->get('blauband_one_click_system.db_connection_service');

        /** @var \Doctrine\DBAL\Connection $guestConnection */
        $guestConnection = $connectionService->createConnection($system->getDbHost(), $system->getDbUsername(), $system->getDbPassword(), $system->getDbName());

        /** @var \Doctrine\DBAL\Connection $hostConnection */
        $hostConnection = $this->container->get('dbal_connection');

        /** @var \BlaubandOneClickSystem\Services\DBCompare\DBCompareService $dbCompareService */
        $dbCompareService = $this->container->get('blauband_one_click_system.db_compare_service');

        /** @var ConfigService $pluginConfig */
        $pluginConfig = $this->container->get('blauband_one_click_system.config_service');

        /** @var DBCompareResult $result */
        //$result = $dbCompareService->compareTable("s_core_shops", $hostConnection, $guestConnection);
        //$this->View()->assign('compare', $result->__toArray());

        /** @var DBCompareGroupResult $groupResult */
        $groupResult = $dbCompareService->compareTables($pluginConfig->get('compare.article.tables'), $hostConnection, $guestConnection);
        $groupResult = $groupResult->__toArray();
        $this->View()->assign('group', $groupResult);
    }
}