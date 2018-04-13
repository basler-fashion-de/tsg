<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandTSG\Services\System\SystemService;
use BlaubandTSG\Services\System\SystemServiceInterface;
use Shopware\Components\Model\ModelManager;
use BlaubandTSG\Models\System;
use BlaubandTSG\Services\System\Local\SystemValidation;
use BlaubandTSG\Services\ConfigService;
use BlaubandTSG\Controllers\Backend\BlaubandEnlightControllerAction;
use BlaubandTSG\Services\System\Local\SetUpSystemService;
use BlaubandTSG\Exceptions\SystemDBAlreadyExists;

class Shopware_Controllers_Backend_BlaubandTSG extends BlaubandEnlightControllerAction implements CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
            'createSystem',
            'deleteSystem',
            'duplicateMediaFolder',
            'systemList',
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
        $parameters = $this->container->get('blauband_tsg.parameter_config_service');
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
        $setUpService = $this->container->get('blauband_tsg.set_up_system_service');
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
        $parameterService = $this->container->get('blauband_tsg.parameter_config_service');
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
            $systemService = $this->container->get("blauband_tsg." . $systemType . "_system_service");
            $systemService->createSystem($systemName, $params);

            if ($params['autoFireCronJob']) {
                $this->fireCronJobAction();
            }

            /** @var SetUpSystemService $setUpService */
            $setUpService = $this->container->get('blauband_tsg.set_up_system_service');
            $this->View()->assign('shopTitle', $setUpService->getDefaultTestShopName());

            $this->sendJsonResponse(
                [
                    'success' => true,
                    'message' => sprintf($snippets->getNamespace('blauband/tsg')->get('duplicateSuccess'), $systemName),
                    'shopTitle' => $setUpService->getDefaultTestShopName()

                ]
            );
        } catch (Exception $e) {
            if ($e instanceof SystemDBAlreadyExists) {
                $this->sendJsonResponse(
                    [
                        'success' => false,
                        'dbOverwrite' => true
                    ]
                );
            } else {
                $this->sendJsonResponse(
                    [
                        'success' => false,
                        'error' => $e->getMessage()
                    ]
                );
            }
        }
    }

    public function duplicateMediaFolderAction()
    {
        try {
            /** @var ModelManager $modelManager */
            $modelManager = $this->container->get('models');
            $systemId = $this->Request()->getParam('id');

            /** @var System $systemModel */
            $systemModel = $modelManager->find(System::class, $systemId);
            $systemModel->setState(SystemService::SYSTEM_STATE_WAITING_GUEST_MEDIA_FOLDER);
            $modelManager->flush($systemModel);

            $this->fireCronJobAction();

            $this->sendJsonResponse(
                [
                    'success' => true,
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
            $validation = $this->container->get('blauband_tsg.system_validation');

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
                    'message' => sprintf($snippets->getNamespace('blauband/tsg')->get('deleteSystemSuccess'), $systemModel->getName())
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
                $html = $this->View()->fetch('backend/blauband_tsg/system_list.tpl');
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
        /** @var \BlaubandTSG\Services\CronJobHelperService $cronJobHelper */
        $cronJobHelper = $this->container->get('blauband_tsg.cron_job_helper_service');
        $cronJobHelper->fire();
        $this->sendJsonResponse(['success' => true]);
    }

    private function checkGuestSystem()
    {
        if (
            $this->container->hasParameter('shopware.blauband.tsg.isguest') &&
            $this->container->getParameter('shopware.blauband.tsg.isguest')
        ) {
            $redirect = array(
                'module' => 'backend',
                'controller' => 'BlaubandTSGGuest',
                'action' => 'index',
            );
            $this->redirect($redirect);
        }
    }
}