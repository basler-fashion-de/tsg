<?php

namespace BlaubandOneClickSystem\Services\System;

use BlaubandOneClickSystem\Services\ConfigService;
use BlaubandOneClickSystem\Services\System\Common\OCSApiService;
use BlaubandOneClickSystem\Services\System\Local\HtAccessService;
use BlaubandOneClickSystem\Services\System\Local\MailService;
use BlaubandOneClickSystem\Services\System\Local\SetUpSystemService;
use BlaubandOneClickSystem\Services\System\Common\DBConnectionService;
use BlaubandOneClickSystem\Services\System\Common\DBDuplicationService;
use BlaubandOneClickSystem\Services\System\Common\CodebaseDuplicationService;
use BlaubandOneClickSystem\Services\System\Local\SystemValidation;
use Doctrine\DBAL\Connection;
use Shopware\Components\Logger;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System;

class Local extends SystemService implements SystemServiceInterface
{
    /**
     * @var Connection
     */
    private $hostConnection;

    /**
     * @var DBConnectionService
     */
    private $dbConnectionService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var SystemValidation
     */
    private $systemValidation;

    /**
     * @var DBDuplicationService
     */
    private $dbDuplicationService;

    /**
     * @var CodebaseDuplicationService
     */
    private $codebaseDuplicationService;

    /**
     * @var SetUpSystemService
     */
    private $setUpSystemService;

    /**
     * @var HtAccessService
     */
    private $htAccessService;

    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @var OCSApiService
     */
    private $ocsApiService;

    /**
     * @var \Shopware_Components_TemplateMail
     */
    private $templateMail;

    /**
     * @var ConfigService
     */
    private $mailConfigService;

    /**
     * @var \Shopware_Components_Config
     */
    private $config;

    /**
     * @var $logger Logger
     */
    private $pluginLogger;

    /**
     * @var string
     */
    private $docRoot;

    public function __construct(
        Connection $hostConnection,
        DBConnectionService $DBConnectionService,
        ModelManager $modelManager,
        SystemValidation $systemValidation,
        DBDuplicationService $dbDuplicationService,
        CodebaseDuplicationService $codebaseDuplicationService,
        SetUpSystemService $setUpSystemService,
        HtAccessService $htAccessService,
        MailService $mailService,
        OCSApiService $ocsApiService,
        \Shopware_Components_TemplateMail $templateMail,
        ConfigService $mailConfigService,
        \Shopware_Components_Config $config,
        Logger $pluginLogger,
        $docRoot
    )
    {
        $this->hostConnection = $hostConnection;
        $this->dbConnectionService = $DBConnectionService;
        $this->modelManager = $modelManager;
        $this->systemValidation = $systemValidation;
        $this->dbDuplicationService = $dbDuplicationService;
        $this->codebaseDuplicationService = $codebaseDuplicationService;
        $this->setUpSystemService = $setUpSystemService;
        $this->htAccessService = $htAccessService;
        $this->mailService = $mailService;
        $this->ocsApiService = $ocsApiService;
        $this->templateMail = $templateMail;
        $this->mailConfigService = $mailConfigService;
        $this->config = $config;
        $this->pluginLogger = $pluginLogger;
        $this->docRoot = $docRoot;
    }

    public function getType()
    {
        return 'local';
    }

    public function createSystem($systemName, $parameters)
    {
        $dbHost = $parameters['dbHost'];
        $dbUser = $parameters['dbUser'];
        $dbPass = $parameters['dbPass'];
        $dbName = $parameters['dbName'];
        $dbOverwrite = $parameters['dbOverwrite'];
        $dbRemote = $parameters['dbRemote'];
        $htpasswordName = $parameters['htpasswdUsername'];
        $htpasswordPass = $parameters['htpasswdPassword'];
        $sendSummery = $parameters['sendSummery'];

        $systemNameUrl = strtolower(str_replace([' '], ['-'], $systemName));


        if ($dbRemote) {
            $guestConnection = $this->ocsApiService->createDatabase();
            $dbHost = $guestConnection->getHost();
            $dbUser = $guestConnection->getUsername();
            $dbPass = $guestConnection->getPassword();
            $dbName = $guestConnection->getDatabase();
        } else {
            $guestConnection = $this->dbConnectionService->createConnection($dbHost, $dbUser, $dbPass);
        }

        $destinationPath = $this->docRoot . '/' . $systemNameUrl;
        $this->systemValidation->validateCurrentProcesses($this->hostConnection);
        $this->systemValidation->validateSystemName($systemName);
        $this->systemValidation->validateDBData($this->hostConnection, $guestConnection, $dbName, $dbOverwrite);
        $this->systemValidation->validatePath($destinationPath);

        try {
            $systemModel = $this->createDBEntry($systemName, $systemNameUrl, $destinationPath, $dbHost, $dbUser, $dbPass, $dbName, $htpasswordName, $htpasswordPass, $parameters);
            $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_WAITING);

            if ($sendSummery) {
                $this->sendMail($systemModel, 'blaubandOCSStarted');
            }
        } catch (\Exception $e) {
            //Rollback
            if (!empty($systemModel)) {
                //Falls der DB Eintrag gelöscht wird werden Gast-DB und Verzeichnis mit gelöscht
                $this->modelManager->remove($systemModel);
                $this->modelManager->flush($systemModel);
            }

            throw $e;
        }
    }

    public function deleteSystem(System $system)
    {
        $this->systemValidation->validateDeleting($system);

        $dbName = $system->getDbName();
        $dbConnection = $this->dbConnectionService->createConnection($system->getDbHost(), $system->getDbUsername(), $system->getDbPassword());

        $this->changeSystemState($system, SystemService::SYSTEM_STATE_DELETING_GUEST_DB);
        try {
            $dbConnection->exec("DROP DATABASE IF EXISTS `$dbName`");
        } catch (\Exception $e) {
        }

        //Verzeichniss löschen
        $this->changeSystemState($system, SystemService::SYSTEM_STATE_DELETING_GUEST_CODEBASE);
        try {
            $this->codebaseDuplicationService->removeDuplicatedCodebase($system->getPath());
        } catch (\Exception $e) {
        }

        $this->changeSystemState($system, SystemService::SYSTEM_STATE_DELETING_HOST_DB_ENTRY);
    }


    /**
     * Helfer Funktionen
     */


    private function createDBEntry($systemName, $systemNameUrl, $destinationPath, $dbHost, $dbUser, $dbPass, $dbName, $htpasswordName, $htpasswordPass, $startParameters)
    {
        $systemModel = new System();
        $systemModel->setName($systemName);
        $systemModel->setPath($destinationPath);
        $systemModel->setUrl('/' . $systemNameUrl);
        $systemModel->setType($this->getType());
        $systemModel->setState(SystemService::SYSTEM_STATE_CREATING_HOST_DB_ENTRY);

        $systemModel->setDbHost($dbHost);
        $systemModel->setDbUsername($dbUser);
        $systemModel->setDbPassword($dbPass);
        $systemModel->setDbName($dbName);

        $systemModel->setHtPasswdUsername($htpasswordName);
        $systemModel->setHtPasswdPassword($htpasswordPass);

        $systemModel->setMediaFolderDuplicated(false);
        $systemModel->setStartParameter($startParameters);

        $this->modelManager->persist($systemModel);
        $this->modelManager->flush($systemModel);
        return $systemModel;
    }

    private function duplicateDB(System $systemModel, Connection $guestConnection)
    {
        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_GUEST_DB);
        $this->dbDuplicationService->createDatabaseAndUse($guestConnection, $systemModel->getDbName());
        $this->dbDuplicationService->duplicateData($this->hostConnection, $guestConnection);

        return true;
    }

    private function duplicateCodeBase(System $systemModel, $sourcePath, $destinationPath)
    {
        $exceptions = [];
        $systems = $this->modelManager->getRepository(System::class)->findAll();
        foreach ($systems as $system) {
            $exceptions[] = $system->getPath();
        }

        $mediaFolders = glob($this->docRoot . '/media/*/*', GLOB_ONLYDIR);
        if (!empty($mediaFolders)) {
            $exceptions = array_merge($exceptions, $mediaFolders);
        }

        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_GUEST_CODEBASE);
        $this->codebaseDuplicationService->duplicateCodeBase($sourcePath, $destinationPath, $exceptions);

        return true;
    }

    private function duplicateMediaFolder(System $systemModel, $sourcePath, $destinationPath)
    {
        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_GUEST_MEDIA_FOLDER);
        $this->codebaseDuplicationService->duplicateCodeBase($sourcePath.'/media', $destinationPath.'/media');

        $systemModel->setMediaFolderDuplicated(true);
        $this->modelManager->flush($systemModel);

        return true;
    }

    private function setUpNewSystem(System $systemModel, Connection $guestConnection)
    {
        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_SET_UP_GUEST_SHOP);
        $this->setUpSystemService->changeShopTitle($guestConnection, $systemModel);
        $this->setUpSystemService->changeShopUrl($guestConnection, $systemModel);
        $this->setUpSystemService->setShopOffline($guestConnection, $systemModel);
        $this->setUpSystemService->setShopMode($guestConnection, $systemModel);
        $this->setUpSystemService->setUpConfigPhp($guestConnection, $systemModel);

        return true;
    }

    private function createHtPasswd(System $systemModel, $destinationPath)
    {
        if (
            empty($systemModel->getHtPasswdUsername()) ||
            empty($systemModel->getHtPasswdPassword())
        ) {
            return false;
        }

        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_SET_UP_GUEST_HTACCESS_HTPASSWD);
        $this->htAccessService->createHtPass($destinationPath, [$systemModel->getHtPasswdUsername() => $systemModel->getHtPasswdPassword()]);

    }

    private function preventMail(System $system, $preventMail)
    {
        if (!$preventMail) {
            return true;
        }

        $this->changeSystemState($system, SystemService::SYSTEM_STATE_CREATING_SET_UP_GUEST_MAILING);
        $this->mailService->preventMail($system->getPath());
    }

    private function sendMail($systemModel, $templateName)
    {
        foreach ($this->mailConfigService->get('mails', true) as $mail) {
            if ($mail['name'] === $templateName) {
                /** @var \Enlight_Components_Mail $mail */
                $mailModel = $this->templateMail->createMail($mail['name'], $systemModel->__toArray());
                $mailModel->addTo($this->config->get('mail'));
                $mailModel->send();
            }
        }
    }

    private function changeSystemState($systemModel, $state)
    {
        $systemModel->setState($state);
        $this->modelManager->flush($systemModel);
    }

    /**
     * CronJob Events
     *
     * Diese Funktionen werden im regelfall von einem CronJob oder einem anderen Process ausgeführt.
     * Diese Prozesse dauern sehr lang und werden im Frontend mit Statusmeldungen angezeigt
     */


    public function executeCreateSystem()
    {
        $systemList = $this->modelManager->getRepository(System::class)->findAll();

        /** @var System $systemModel */
        foreach ($systemList as $systemModel) {
            if ($systemModel->getState() === SystemService::SYSTEM_STATE_CREATING_WAITING) {
                try {
                    $guestConnection = $this->dbConnectionService->createConnection(
                        $systemModel->getDbHost(),
                        $systemModel->getDbUsername(),
                        $systemModel->getDbPassword(),
                        $systemModel->getDbName()
                    );

                    $this->duplicateDB($systemModel, $guestConnection);
                    $this->duplicateCodeBase($systemModel, $this->docRoot, $systemModel->getPath());
                    $this->setUpNewSystem($systemModel, $guestConnection);
                    $this->createHtPasswd($systemModel, $systemModel->getPath());
                    $this->preventMail($systemModel, $systemModel->getStartParameter()['preventMail']);

                    if(!$systemModel->getStartParameter()['skipMediaFolder']){
                        $this->duplicateMediaFolder($systemModel, $this->docRoot, $systemModel->getPath());
                    }

                    $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_READY);

                    if ($systemModel->getSummeryMail()) {
                        $this->sendMail($systemModel, 'blaubandOCSFinished');
                    }
                } catch (\Exception $e) {
                    $this->modelManager->remove($systemModel);
                    $this->modelManager->flush($systemModel);

                    $this->pluginLogger->addError("Blauband OCS: ".$e->getMessage());

                    throw $e;
                }
            }
        }
    }

    public function executeDuplicateMediaFolder(){
        $systemList = $this->modelManager->getRepository(System::class)->findAll();

        /** @var System $systemModel */
        foreach ($systemList as $systemModel) {
            if ($systemModel->getState() === SystemService::SYSTEM_STATE_WAITING_GUEST_MEDIA_FOLDER) {
                $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_GUEST_MEDIA_FOLDER);
                $this->duplicateMediaFolder($systemModel, $this->docRoot, $systemModel->getPath());
                $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_READY);
            }
        }
    }

    public function executeDeleteSystem()
    {
        $systemList = $this->modelManager->getRepository(System::class)->findAll();

        /** @var System $systemModel */
        foreach ($systemList as $systemModel) {
            if ($systemModel->getState() === SystemService::SYSTEM_STATE_DELETING_WAITING) {
                $this->modelManager->remove($systemModel);
            }
        }

        $this->modelManager->flush();
    }
}
