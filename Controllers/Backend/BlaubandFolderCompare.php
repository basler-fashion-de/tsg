<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandOneClickSystem\Services\SystemServiceInterface;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System;
use BlaubandOneClickSystem\Services\DBCompare\FolderCompareService;
use BlaubandOneClickSystem\Services\System\Common\CodebaseDuplicationService;
use BlaubandOneClickSystem\Exceptions\MissingParameterException;
use BlaubandOneClickSystem\Controllers\Backend\BlaubandEnlightControllerAction;
use BlaubandOneClickSystem\Services\ConfigService;

class Shopware_Controllers_Backend_BlaubandFolderCompare extends BlaubandEnlightControllerAction implements CSRFWhitelistAware
{
    /** @var ModelManager $modelManager */
    private $modelManager;

    /** @var  $snippets */
    private $snippets;

    /** @var FolderCompareService $folderCompareService */
    private $folderCompareService;

    /** @var CodebaseDuplicationService */
    private $codebaseDuplicationService;

    /** @var ConfigService $pluginConfig */
    private $pluginConfig;

    private $docRoot;

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
        $this->folderCompareService = $this->container->get('blauband_one_click_system.folder_compare_service');
        $this->codebaseDuplicationService = $this->container->get('blauband_one_click_system.codebase_duplication_service');
        $this->pluginConfig = $this->container->get('blauband_one_click_system.config_service');
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
                    $this->snippets->getNamespace('blauband/ocs')->get('missingParameter')
                );
            }

            /** @var System $system */
            $system = $this->modelManager->getRepository(System::class)->find($systemId);
            $path = $this->pluginConfig->get("compare.$compareGroup.folder");
            $hostPath = $this->docRoot.'/'.$path;
            $guestPath = $system->getPath().'/'.$path;

            /** @var FolderCompareResult $result */
            $result = $this->folderCompareService->compareFolder($hostPath, $guestPath);
            $result = $result->__toArray();

            $this->View()->assign('result', $result);
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

            /** @var System $system */
            $system = $this->modelManager->getRepository(System::class)->find($systemId);
            $path = $this->pluginConfig->get("compare.$compareGroup.folder");
            $hostPath = $this->docRoot.'/'.$path;
            $guestPath = $system->getPath().'/'.$path;

            $this->codebaseDuplicationService->duplicateCodeBase($guestPath, $hostPath);
        } catch (Exception $e) {
            $this->View()->assign('error', $e->getMessage());
        }
    }
}