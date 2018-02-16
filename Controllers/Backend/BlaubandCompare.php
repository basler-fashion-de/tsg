<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandOneClickSystem\Services\SystemServiceInterface;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System;
use BlaubandOneClickSystem\Services\DBCompare\DBCompareService;
use BlaubandOneClickSystem\Services\System\Common\DBDuplicationService;
use BlaubandOneClickSystem\Exceptions\MissingParameterException;
use BlaubandOneClickSystem\Controllers\Backend\BlaubandEnlightControllerAction;

class Shopware_Controllers_Backend_BlaubandCompare extends BlaubandEnlightControllerAction implements CSRFWhitelistAware
{
    /** @var ModelManager $modelManager */
    private $modelManager;
    /** @var  $snippets */
    private $snippets;

    /** @var \Doctrine\DBAL\Connection $hostConnection */
    private $hostConnection;

    /** @var DBCompareService $dbCompareService */
    private $dbCompareService;

    /** @var ConfigService $pluginConfig */
    private $pluginConfig;

    /** @var DBConnectionService $connectionService */
    private $connectionService;

    /** @var DBDuplicationService $dbDuplicationService */
    private $dbDuplicationService;

    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
            'commit'
        ];
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->modelManager = $this->container->get('models');
        $this->snippets = $this->container->get('snippets');
        $this->hostConnection = $this->container->get('dbal_connection');
        $this->dbCompareService = $this->container->get('blauband_one_click_system.db_compare_service');
        $this->pluginConfig = $this->container->get('blauband_one_click_system.config_service');
        $this->connectionService = $this->container->get('blauband_one_click_system.db_connection_service');
        $this->dbDuplicationService = $this->container->get('blauband_one_click_system.db_duplication_service');
    }


    /**
     * Startseite
     */
    public function indexAction()
    {
        try {
            $systemId = $this->Request()->getParam('id');
            $compareGroup = $this->Request()->getParam('group');


            if (empty($systemId) || empty($compareGroup)) {
                throw new MissingParameterException(
                    $this->snippets->getNamespace('blauband/ocs')->get('missingParameter')
                );
            }

            $system = $this->modelManager->getRepository(System::class)->find($systemId);

            /** @var \Doctrine\DBAL\Connection $guestConnection */
            $guestConnection = $this->connectionService->createConnection($system->getDbHost(), $system->getDbUsername(), $system->getDbPassword(), $system->getDbName());

            /** @var DBCompareGroupResult $groupResult */
            $groupResult = $this->dbCompareService->compareTables($this->pluginConfig->get("compare.$compareGroup.tables"), $this->hostConnection, $guestConnection);
            $groupResult = $groupResult->__toArray();
            $this->View()->assign('result', $groupResult);
            $this->View()->assign('id', $systemId);
            $this->View()->assign('group', $compareGroup);
        } catch (Exception $e) {
            $this->View()->assign('error', $e->getMessage());
        }
    }

    public function commitAction()
    {
        try {
            $systemId = $this->Request()->getParam('id');
            $compareGroup = $this->Request()->getParam('group');

            if (empty($systemId) || empty($compareGroup)) {
                throw new MissingParameterException(
                    $this->snippets->getNamespace('blauband/ocs')->get('missingParameter')
                );
            }

            $system = $this->modelManager->getRepository(System::class)->find($systemId);

            /** @var \Doctrine\DBAL\Connection $guestConnection */
            $guestConnection = $this->connectionService->createConnection($system->getDbHost(), $system->getDbUsername(), $system->getDbPassword(), $system->getDbName());
            $tables = $this->pluginConfig->get("compare.$compareGroup.tables");
            $this->dbDuplicationService->duplicateData($guestConnection, $this->hostConnection, $tables);
        } catch (Exception $e) {
            $this->View()->assign('error', $e->getMessage());
        }
    }
}