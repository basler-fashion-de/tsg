<?php

namespace BlaubandOneClickSystem\Services\System;

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
    private $shopwareConnection;

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
     * @var string
     */
    private $docRoot;

    public function __construct(
        Connection $shopwareConnection,
        DBConnectionService $DBConnectionService,
        ModelManager $modelManager,
        SystemValidation $systemValidation,
        DBDuplicationService $dbDuplicationService,
        CodebaseDuplicationService $codebaseDuplicationService,
        $docRoot
    )
    {
        $this->shopwareConnection = $shopwareConnection;
        $this->dbConnectionService = $DBConnectionService;
        $this->modelManager = $modelManager;
        $this->systemValidation = $systemValidation;
        $this->dbDuplicationService = $dbDuplicationService;
        $this->codebaseDuplicationService = $codebaseDuplicationService;
        $this->docRoot = $docRoot;
    }

    public function getType()
    {
        return 'local';
    }

    public function createSystem($systemName, $dbHost, $dbUser, $dbPass, $dbName, $dbOverwrite)
    {
        $guestConnection = $this->dbConnectionService->createConnection($dbHost, $dbUser, $dbPass);
        $destinationPath = $this->docRoot . '/' . strtolower($systemName);
        $this->systemValidation->validateSystemName($systemName);
        $this->systemValidation->validateDBData($guestConnection, $dbName, $dbOverwrite);
        $this->systemValidation->validatePath($destinationPath);

        try {
            $systemModel = $this->createDBEntry($systemName, $destinationPath);
            $dbCreated = $this->duplicateDB($systemModel, $guestConnection, $dbName);
            $codebaseCreated = $this->duplicateCodeBase($systemModel, $this->docRoot, $destinationPath);


            $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_READY);
        } catch (\Exception $e) {
            //Rollback
            if (!empty($systemModel)) {
                $this->modelManager->remove($systemModel);
                $this->modelManager->flush($systemModel);
            }

            if ($dbCreated && !$dbOverwrite) {
                $guestConnection->exec("DROP DATABASE IF EXISTS `$dbName`");
            }

            if ($codebaseCreated) {
                @rmdir($destinationPath);
            }

            throw $e;
        }
    }

    /**
     * @param $systemName
     * @return System
     */
    private function createDBEntry($systemName, $destinationPath)
    {
        $systemModel = new System();
        $systemModel->setName($systemName);
        $systemModel->setPath($destinationPath);
        $systemModel->setType($this->getType());
        $systemModel->setState(SystemService::SYSTEM_STATE_CREATING_HOST_DB_ENTRY);

        $this->modelManager->persist($systemModel);
        $this->modelManager->flush($systemModel);
        return $systemModel;
    }

    private function duplicateDB($systemModel, $guestConnection, $dbName)
    {
        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_GUEST_DB);
        $this->dbDuplicationService->createDatabaseAndUse($guestConnection, $dbName);
        $this->dbDuplicationService->duplicateData($this->shopwareConnection, $guestConnection);

        return true;
    }

    private function duplicateCodeBase($systemModel, $sourcePath, $destinationPath)
    {
        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_GUEST_CODEBASE);
        $this->codebaseDuplicationService->duplicateCodeBase($sourcePath, $destinationPath);

        return true;
    }

    private function changeSystemState($systemModel, $state)
    {
        $systemModel->setState($state);
        $this->modelManager->flush($systemModel);
    }

    public function deleteSystem($id)
    {
        var_dump("DELETE");
    }
}
