<?php

use Shopware\Components\CSRFWhitelistAware;
use Shopware\Components\Model\ModelManager;
use BlaubandTSG\Models\System;
use BlaubandTSG\Services\DBCompare\DBCompareService;
use BlaubandTSG\Services\DBCompare\DBCompareGroupResult;
use BlaubandTSG\Services\FolderCompare\FolderCompareResult;
use BlaubandTSG\Services\FolderCompare\FolderCompareService;
use BlaubandTSG\Services\System\Common\DBDuplicationService;
use BlaubandTSG\Services\System\Common\CodebaseDuplicationService;
use BlaubandTSG\Exceptions\MissingParameterException;
use BlaubandTSG\Controllers\Backend\BlaubandEnlightControllerAction;
use BlaubandTSG\Services\ConfigService;
use BlaubandTSG\Services\System\Common\DBConnectionService;

class Shopware_Controllers_Backend_BlaubandCompare extends BlaubandEnlightControllerAction implements CSRFWhitelistAware
{
    /** @var ModelManager $modelManager */
    private $modelManager;

    /** @var Shopware_Components_Snippet_Manager $snippets */
    private $snippets;

    /** @var \Doctrine\DBAL\Connection $hostConnection */
    private $hostConnection;

    /** @var DBCompareService $dbCompareService */
    private $dbCompareService;

    /** @var FolderCompareService $folderCompareService */
    private $folderCompareService;

    /** @var ConfigService $pluginConfig */
    private $pluginConfig;

    /** @var DBConnectionService $connectionService */
    private $connectionService;

    /** @var DBDuplicationService $dbDuplicationService */
    private $dbDuplicationService;

    /** @var CodebaseDuplicationService $codebaseDuplicationService */
    private $codebaseDuplicationService;

    private $docRoot;

    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
            'loadCompare',
            'commit'
        ];
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->modelManager = $this->container->get('models');
        $this->snippets = $this->container->get('snippets');
        $this->hostConnection = $this->container->get('dbal_connection');
        $this->dbCompareService = $this->container->get('blauband_tsg.db_compare_service');
        $this->folderCompareService = $this->container->get('blauband_tsg.folder_compare_service');
        $this->pluginConfig = $this->container->get('blauband_tsg.compare_config_service');
        $this->connectionService = $this->container->get('blauband_tsg.db_connection_service');
        $this->dbDuplicationService = $this->container->get('blauband_tsg.db_duplication_service');
        $this->codebaseDuplicationService = $this->container->get('blauband_tsg.codebase_duplication_service');
        $this->docRoot = $this->container->getParameter('kernel.root_dir');

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
                    $this->snippets->getNamespace('blauband/tsg')->get('missingParameter')
                );
            }

            $tables = $this->pluginConfig->get("compare.$compareGroup.tables");
            $paths = $this->pluginConfig->get("compare.$compareGroup.folders");

            $attributes = $this->pluginConfig->get("compare.$compareGroup.@attributes");
            $commit = !(isset($attributes['commit']) && $attributes['commit'] === "false"); //Default true

            $this->View()->assign('id', $systemId);
            $this->View()->assign('group', $compareGroup);
            $this->View()->assign('commit', $commit);
            $this->View()->assign('maxCount', count($tables) + count($paths));

        } catch (\Exception $e) {
            $this->View()->assign('error', $e->getMessage());
        }
    }

    public function loadCompareAction()
    {
        try {
            $systemId = $this->Request()->getParam('id');
            $compareGroup = $this->Request()->getParam('group');
            $count = $this->Request()->getParam('count');

            if (empty($systemId) || empty($compareGroup)) {
                throw new MissingParameterException(
                    $this->snippets->getNamespace('blauband/tsg')->get('missingParameter')
                );
            }

            /** @var System $system */
            $system = $this->modelManager->getRepository(System::class)->find($systemId);

            $tables = $this->pluginConfig->get("compare.$compareGroup.tables", true);
            $paths = $this->pluginConfig->get("compare.$compareGroup.folders", true);

            if($count <= count($tables)){
                $table = $tables[$count-1];

                if(empty($table)){
                    $this->sendJsonResponse(['success' => false]);
                }

                /** @var \Doctrine\DBAL\Connection $guestConnection */
                $guestConnection = $this->connectionService->createConnection($system->getDbHost(), $system->getDbUsername(), $system->getDbPassword(), $system->getDbName(), $system->getDbPort());
                $dbResult = $this->dbCompareService->compareTable($table, $this->hostConnection, $guestConnection);
                $dbResult = $dbResult->__toArray();

                $this->View()->assign('compare', $dbResult);
                $html = $this->View()->fetch('backend/blauband_compare/compare_table.tpl');

                $this->sendJsonResponse(
                    [
                        'success' => true,
                        'type' => 'table',
                        'html' => trim($html)
                    ]
                );
            } elseif($count > count($tables) && $count <= count($tables)+count($paths)){
                $path = $paths[$count-count($tables)-1];

                if(empty($path)){
                    $this->sendJsonResponse(['success' => false]);
                }

                $hostPath = $this->docRoot.'/'.$path;
                $guestPath = $system->getPath().'/'.$path;

                /** @var FolderCompareResult $result */
                $folderResult = $this->folderCompareService->compareFolder([$hostPath], [$guestPath]);
                $folderResult = $folderResult->__toArray();

                $this->View()->assign('compare', $folderResult);
                $html = $this->View()->fetch('backend/blauband_compare/compare_folder.tpl');

                $this->sendJsonResponse(
                    [
                        'success' => true,
                        'type' => 'folder',
                        'html' => trim($html)
                    ]
                );
            } else{
                die($count);

                //Parameter passt nicht mehr
                return;
            }
        } catch (\Exception $e) {
            $this->sendJsonResponse(
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            );
        }
    }

    public function commitAction()
    {
        try {
            $systemId = $this->Request()->getParam('id');
            $compareGroup = $this->Request()->getParam('group');

            if (empty($systemId) || empty($compareGroup)) {
                throw new MissingParameterException(
                    $this->snippets->getNamespace('blauband/tsg')->get('missingParameter')
                );
            }

            /** @var System $system */
            $system = $this->modelManager->getRepository(System::class)->find($systemId);
            $tables = $this->pluginConfig->get("compare.$compareGroup.tables", true);
            $paths = $this->pluginConfig->get("compare.$compareGroup.folders", true);

            /** @var \Doctrine\DBAL\Connection $guestConnection */
            $guestConnection = $this->connectionService->createConnection($system->getDbHost(), $system->getDbUsername(), $system->getDbPassword(), $system->getDbName(), $system->getDbPort());
            $this->dbDuplicationService->duplicateData($guestConnection, $this->hostConnection, $tables);

            foreach ($paths as $path){
                $hostPath = $this->docRoot.'/'.$path;
                $guestPath = $system->getPath().'/'.$path;

                $this->codebaseDuplicationService->duplicateCodeBase($guestPath, $hostPath);
            }

        } catch (\Exception $e) {
            $this->View()->assign('error', $e->getMessage());
        }
    }
}