<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandOneClickSystem\Services\SystemServiceInterface;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System;

class Shopware_Controllers_Backend_BlaubandOneClickSystem extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
            'createSystem',
            'deleteSystem'
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
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->container->get('dbal_connection');

        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
        $systemList = $modelManager->getRepository(System::class)->findAll();
        $systemListArray = $modelManager->toArray($systemList);

        /*$this->View()->assign("systems", [
            ['name' => 'Test-System 1', 'createDate' => '1.1.2018', 'path' => 'asd', 'state' => SystemService::SYSTEM_STATE_READY, 'type' => 'local'],
            ['name' => 'Test-System 2', 'createDate' => '2.1.2018', 'path' => 'asd', 'state' => SystemService::SYSTEM_STATE_READY, 'type' => 'local']
        ]);*/

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

        try {
            /** @var SystemServiceInterface $localSystemService */
            $systemService = $this->container->get("blauband_one_click_system." . $systemType . "_system_service");
            $systemService->createSystem($systemName, $dbHost, $dbUser, $dbPass, $dbName, $dbOverwrite);
            $this->sendJsonResponse(
                [
                    'success' => true,
                    'message' => $snippets->getNamespace('blaubandOneClickSystem')->get('duplicateSuccess', "Ihr System [$systemName] konnte erfolgreich eingerichtet werden.")

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
        try{
            /** @var ModelManager $modelManager */
            $modelManager = $this->container->get('models');
            /** @var Enlight_Components_Snippet_Manager $snippets */
            $snippets = $this->container->get('snippets');

            $systemId = $this->Request()->getParam('id');

            /** @var System $systemModel */
            $systemModel = $modelManager->find(System::class, $systemId);
            $systemName = $systemModel->getName();
            $modelManager->remove($systemModel);
            $modelManager->flush($systemModel);

            $this->sendJsonResponse(
                [
                    'success' => true,
                    'message' => $snippets->getNamespace('blaubandOneClickSystem')->get('deleteSystemSuccess', "Das System [$systemName] konnte erfolgreich gelöscht werden.")

                ]
            );
        }catch (Exception $e){
            $this->sendJsonResponse(
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            );
        }

    }

    private function sendJsonResponse($data)
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->Response()->setBody(json_encode($data));
        $this->Response()->setHeader('Content-type', 'application/json', true);
    }
}