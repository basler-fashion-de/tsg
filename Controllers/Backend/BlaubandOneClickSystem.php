<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandOneClickSystem\Services\System\SystemService;
use BlaubandOneClickSystem\Services\System\SystemServiceInterface;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System;
use BlaubandOneClickSystem\Services\System\Local\SystemValidation;
use BlaubandOneClickSystem\Services\ConfigService;
use BlaubandOneClickSystem\Controllers\Backend\BlaubandEnlightControllerAction;
use BlaubandOneClickSystem\Services\System\Local\SetUpSystemService;

class Shopware_Controllers_Backend_BlaubandOneClickSystem extends BlaubandEnlightControllerAction implements CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
            'createSystem',
            'deleteSystem',
            'systemList',
            'guestSystemError',
            'fireCronJob'
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

        /** @var ConfigService $connection */
        $parameters = $this->container->get('blauband_one_click_system.parameter_config_service');
        $groups = $parameters->get('groups');

        //Defaults überschreiben
        foreach ($groups as &$group) {
            foreach ($group['parameters'] as &$parameter) {
                if ($parameter['title'] == 'dbHost') {
                    $parameter['default'] = $connection->getHost();
                }

                if ($parameter['title'] == 'dbUser') {
                    $parameter['default'] = $connection->getUsername();
                }

                if ($parameter['title'] == 'dbPass') {
                    $parameter['default'] = $connection->getPassword();
                }
            }
        }

        $this->View()->assign('actionFields', $groups);

        /** @var SetUpSystemService $setUpService */
        $setUpService = $this->container->get('blauband_one_click_system.set_up_system_service');
        $this->View()->assign('shopTitle', $setUpService->getDefaultTestShopName());
    }

    /**
     *  Ajax aufruf um System zu erstellen
     */
    public function createSystemAction()
    {
        $snippets = $this->container->get('snippets');

        $params = $this->Request()->getParams();

        $systemName = $params['name'];
        $systemType = $params['type'];

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        /** @var ConfigService $parameterService */
        $parameterService = $this->container->get('blauband_one_click_system.parameter_config_service');
        $groups = $parameterService->get('groups');

        //On|Off zu true|false
        foreach ($groups as $group) {
            foreach ($group['parameters'] as $parameter) {
                if ($parameter['type'] == 'checkbox') {
                    $params[$parameter['title']] = $params[$parameter['title']] === 'on';
                }
            }
        }

        if (!$params['htpasswd']) {
            $params['htpasswdUsername'] = null;
            $params['htpasswdPassword'] = null;
        }

        try {
            /** @var SystemServiceInterface $localSystemService */
            $systemService = $this->container->get("blauband_one_click_system." . $systemType . "_system_service");
            $systemService->createSystem($systemName, $params);

            if ($params['autoFireCronJob']) {
                $this->fireCronJobAction();
            }

            /** @var SetUpSystemService $setUpService */
            $setUpService = $this->container->get('blauband_one_click_system.set_up_system_service');
            $this->View()->assign('shopTitle', $setUpService->getDefaultTestShopName());

            $this->sendJsonResponse(
                [
                    'success' => true,
                    'message' => sprintf($snippets->getNamespace('blauband/ocs')->get('duplicateSuccess'), $systemName),
                    'shopTitle' => $setUpService->getDefaultTestShopName()

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

            $systemModel->setState(SystemService::SYSTEM_STATE_DELETING_WAITING);
            $modelManager->flush($systemModel);

            $this->fireCronJobAction();

            $this->sendJsonResponse(
                [
                    'success' => true,
                    'message' => sprintf($snippets->getNamespace('blauband/ocs')->get('deleteSystemSuccess'), $systemModel->getName())
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

    public function fireCronJobAction()
    {
        /** @var \BlaubandOneClickSystem\Services\CronJobHelperService $cronJobHelper */
        $cronJobHelper = $this->container->get('blauband_one_click_system.cron_job_helper_service');
        $cronJobHelper->fire();
        $this->sendJsonResponse(['success' => true]);
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