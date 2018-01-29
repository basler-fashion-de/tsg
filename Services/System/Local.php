<?php

namespace BlaubandOneClickSystem\Services\System;

use BlaubandOneClickSystem\Services\System\Local\HtAccessService;
use BlaubandOneClickSystem\Services\System\Local\MailService;
use BlaubandOneClickSystem\Services\System\Local\SetUpSystemService;
use BlaubandOneClickSystem\Services\System\Local\DBConnectionService;
use BlaubandOneClickSystem\Services\System\Local\DBDuplicationService;
use BlaubandOneClickSystem\Services\System\Local\CodebaseDuplicationService;
use BlaubandOneClickSystem\Services\System\Local\SystemValidation;
use BlaubandOneClickSystem\Services\SystemService;
use BlaubandOneClickSystem\Services\SystemServiceInterface;
use Doctrine\DBAL\Connection;
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
        $preventMail = $parameters['preventMail'];
        $skipMedia = $parameters['skipMedia'];
        $htpasswordName = $parameters['htpasswordName'];
        $htpasswordPass = $parameters['htpasswordPass'];

        $guestConnection = $this->dbConnectionService->createConnection($dbHost, $dbUser, $dbPass);
        $destinationPath = $this->docRoot . '/' . strtolower($systemName);
        $this->systemValidation->validateCurrentProcesses($this->hostConnection);
        $this->systemValidation->validateSystemName($systemName);
        $this->systemValidation->validateDBData($this->hostConnection, $guestConnection, $dbName, $dbOverwrite);
        $this->systemValidation->validatePath($destinationPath);

        try {
            $systemModel = $this->createDBEntry($systemName, $destinationPath, $dbHost, $dbUser, $dbPass, $dbName, $htpasswordName, $htpasswordPass, $preventMail, $skipMedia);
            $this->duplicateDB($systemModel, $guestConnection);
            $this->duplicateCodeBase($systemModel, $this->docRoot, $destinationPath, $skipMedia);
            $this->setUpNewSystem($systemModel, $guestConnection);
            $this->createHtPasswd($systemModel, $destinationPath);
            $this->preventMail($systemModel, $preventMail);

            $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_READY);
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

    /**
     * @param $systemName
     * @return System
     */
    private function createDBEntry($systemName, $destinationPath, $dbHost, $dbUser, $dbPass, $dbName, $htpasswordName, $htpasswordPass, $preventMail, $skipMedia)
    {
        $systemModel = new System();
        $systemModel->setName($systemName);
        $systemModel->setPath($destinationPath);
        $systemModel->setUrl('/' . strtolower($systemName));
        $systemModel->setType($this->getType());
        $systemModel->setState(SystemService::SYSTEM_STATE_CREATING_HOST_DB_ENTRY);

        $systemModel->setDbHost($dbHost);
        $systemModel->setDbUsername($dbUser);
        $systemModel->setDbPassword($dbPass);
        $systemModel->setDbName($dbName);

        $systemModel->setHtPasswdUsername($htpasswordName);
        $systemModel->setHtPasswdPassword($htpasswordPass);

        $systemModel->setPreventMail($preventMail);
        $systemModel->setSkipMedia($skipMedia);

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

    private function duplicateCodeBase(System $systemModel, $sourcePath, $destinationPath, $skipMedia)
    {
        $exceptions = [];
        $systems = $this->modelManager->getRepository(System::class)->findAll();
        foreach ($systems as $system) {
            $exceptions[] = $system->getPath();
        }

        if ($skipMedia === true) {
            $mediaFolders = glob($this->docRoot . '/media/*/*', GLOB_ONLYDIR);
            if (!empty($mediaFolders)) {
                $exceptions = array_merge($exceptions, $mediaFolders);
            }

        }

        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_GUEST_CODEBASE);
        $this->codebaseDuplicationService->duplicateCodeBase($sourcePath, $destinationPath, $exceptions);

        return true;
    }

    private function setUpNewSystem(System $systemModel, Connection $guestConnection)
    {
        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_SET_UP_HOST_SHOP);
        $this->setUpSystemService->changeShopTitle($guestConnection, $systemModel);
        $this->setUpSystemService->changeShopUrl($guestConnection, $systemModel);
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
        $this->mailService->preventMail($system);
    }

    private function changeSystemState($systemModel, $state)
    {
        $systemModel->setState($state);
        $this->modelManager->flush($systemModel);
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
}
