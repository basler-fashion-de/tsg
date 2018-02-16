<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandOneClickSystem\Services\SystemServiceInterface;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System;
use BlaubandOneClickSystem\Services\System\Local\SystemValidation;
use BlaubandOneClickSystem\Controllers\Backend\BlaubandEnlightControllerAction;

class Shopware_Controllers_Backend_BlaubandOneClickSystem extends BlaubandEnlightControllerAction implements CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
            'createSystem',
            'deleteSystem',
            'systemList',
            'guestSystemError'
        ];
    }

    /**
     * Pre dispatch method
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->checkGuestSystem();
    }

    /**
     * Startseite
     */
    public function indexAction()
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->container->get('dbal_connection');

        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
        $systemList = $modelManager->getRepository(System::class)->findAll();
        $systemListArray = $modelManager->toArray($systemList);

        $this->View()->assign("systems", $systemListArray);

        $this->View()->assign('dbhost', $connection->getHost());
        $this->View()->assign('dbuser', $connection->getUsername());
        $this->View()->assign('dbpass', $connection->getPassword());
    }

    /**
     *  Ajax aufruf um System zu erstellen
     */
    public function createSystemAction()
    {
        $snippets = $this->container->get('snippets');

        $systemName = $this->Request()->getParam('name');
        $systemType = $this->Request()->getParam('type');

        $dbHost = $this->Request()->getParam('dbhost');
        $dbUser = $this->Request()->getParam('dbuser');
        $dbPass = $this->Request()->getParam('dbpass');
        $dbName = $this->Request()->getParam('dbname');
        $dbOverwrite = $this->Request()->getParam('dboverwrite') == 'on';
        $dbRemote = $this->Request()->getParam('dbremote') == 'on';

        $preventMail = $this->Request()->getParam('preventmail') == 'on';
        $skipMedia = $this->Request()->getParam('skipmedia') == 'on';

        $shopOwnerMail = $this->Request()->getParam('shopowner');

        $htpasswordPass = $htpasswordName = null;
        if (
            $this->Request()->getParam('htpasswd') == 'on' &&
            !empty($this->Request()->getParam('htpasswdusername')) &&
            !empty($this->Request()->getParam('htpasswdpassword'))
        ) {
            $htpasswordName = $this->Request()->getParam('htpasswdusername');
            $htpasswordPass = $this->Request()->getParam('htpasswdpassword');
        }

        try {
            $para = [
                'dbHost' => $dbHost,
                'dbUser' => $dbUser,
                'dbPass' => $dbPass,
                'dbName' => $dbName,
                'dbOverwrite' => $dbOverwrite,
                'dbRemote' => $dbRemote,
                'preventMail' => $preventMail,
                'skipMedia' => $skipMedia,
                'htpasswordName' => $htpasswordName,
                'htpasswordPass' => $htpasswordPass,
                'shopOwnerMail' => $shopOwnerMail
            ];
            /** @var SystemServiceInterface $localSystemService */
            $systemService = $this->container->get("blauband_one_click_system." . $systemType . "_system_service");
            $systemService->createSystem($systemName, $para);
            $this->sendJsonResponse(
                [
                    'success' => true,
                    'message' => sprintf($snippets->getNamespace('blauband/ocs')->get('duplicateSuccess'), $systemName)

                ]
            );
        } catch (Exception $e) {
            $this->sendJsonResponse(
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     *  Ajax aufruf um System zu erstellen
     */
    public function deleteSystemAction()
    {
        try {
            /** @var ModelManager $modelManager */
            $modelManager = $this->container->get('models');
            /** @var Enlight_Components_Snippet_Manager $snippets */
            $snippets = $this->container->get('snippets');
            /** @var SystemValidation $validation */
            $validation = $this->container->get('blauband_one_click_system.system_validation');

            $systemId = $this->Request()->getParam('id');

            /** @var System $systemModel */
            $systemModel = $modelManager->find(System::class, $systemId);
            $validation->validateCurrentProcesses();
            $validation->validateDeleting($systemModel);
            $systemName = $systemModel->getName();
            $modelManager->remove($systemModel);
            $modelManager->flush($systemModel);

            $this->sendJsonResponse(
                [
                    'success' => true,
                    'message' => sprintf($snippets->getNamespace('blauband/ocs')->get('deleteSystemSuccess'), $systemName)
                ]
            );
        } catch (Exception $e) {
            $this->sendJsonResponse(
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            );
        }
    }

    public function systemListAction()
    {
        try {
            /** @var ModelManager $modelManager */
            $modelManager = $this->container->get('models');
            /** @var System[] $systemModels */
            $systemModels = $modelManager->getRepository(System::class)->findAll();
            $systemModelsArray = $modelManager->toArray($systemModels);

            if (!empty($systemModelsArray)) {
                $this->View()->assign('systems', $systemModelsArray);
                $html = $this->View()->fetch('backend/blauband_one_click_system/system_list.tpl');
            } else {
                $html = '';
            }

            $this->sendJsonResponse(
                [
                    'success' => true,
                    'html' => $html
                ]
            );
        } catch (Exception $e) {
            $this->sendJsonResponse(
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            );
        }
    }

    public function guestSystemErrorAction()
    {

    }

    private function checkGuestSystem()
    {
        $configPath = Shopware()->DocPath() . "/config.php";
        $config = include $configPath;

        if (
            isset($config['blauband']['ocs']['isGuest']) &&
            $config['blauband']['ocs']['isGuest'] &&
            $this->Request()->getActionName() != 'guestSystemError'
        ) {
            $redirect = array(
                'module' => 'backend',
                'controller' => 'BlaubandOneClickSystem',
                'action' => 'guestSystemError',
            );
            $this->redirect($redirect);
        }
    }
}