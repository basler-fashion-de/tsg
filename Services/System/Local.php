<?php

namespace BlaubandOneClickSystem\Services\System;

use BlaubandOneClickSystem\Services\DBConnectionService;
use BlaubandOneClickSystem\Services\SystemService;
use BlaubandOneClickSystem\Services\SystemServiceInterface;
use BlaubandOneClickSystem\Services\SystemValidation;
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
     * @var string
     */
    private $docRoot;

    public function __construct(
        Connection $shopwareConnection,
        DBConnectionService $DBConnectionService,
        ModelManager $modelManager,
        SystemValidation $systemValidation,
        $docRoot
    )
    {
        $this->shopwareConnection = $shopwareConnection;
        $this->dbConnectionService = $DBConnectionService;
        $this->modelManager = $modelManager;
        $this->systemValidation = $systemValidation;
        $this->docRoot = $docRoot;
    }

    public function getType()
    {
        return 'local';
    }

    public function createSystem($systemName, $dbHost, $dbUser, $dbPass, $dbName, $dbOverwrite)
    {
        $guestConnection = $this->dbConnectionService->createConnection($dbHost, $dbUser, $dbPass);
        $this->systemValidation->validateSystemName($systemName);
        $this->systemValidation->validateDBData($guestConnection, $dbName, $dbOverwrite);

        try {
            $systemModel = $this->createDBEntry($systemName);
            $dbCreated = $this->duplicateDB($guestConnection, $systemModel, $dbName);
            $codeBaseCreated = $this->duplicateCodeBase();
        } catch (\Exception $e) {
            //Rollback
            if (!empty($systemModel)) {
                $this->modelManager->remove($systemModel);
                $this->modelManager->flush($systemModel);
            }

            if ($dbCreated && !$dbOverwrite) {
                $guestConnection->exec("DROP DATABASE IF EXISTS `$dbName`");
            }

            throw $e;
        }
    }

    /**
     * @param $systemName
     * @return System
     */
    private function createDBEntry($systemName)
    {
        $systemModel = new System();
        $systemModel->setName($systemName);
        $systemModel->setPath($this->docRoot . '/' . $systemName);
        $systemModel->setType($this->getType());
        $systemModel->setState(SystemService::SYSTEM_STATE_CREATING_HOST_DB_ENTRY);

        $this->modelManager->persist($systemModel);
        $this->modelManager->flush($systemModel);
        return $systemModel;
    }

    private function duplicateDB($guestConnection, $systemModel, $dbName)
    {
        $this->changeSystemState($systemModel, SystemService::SYSTEM_STATE_CREATING_GUEST_DB);
        $this->dbConnectionService->createDatabaseAndUse($guestConnection, $dbName);
        $this->dbConnectionService->duplicateData($this->shopwareConnection, $guestConnection);

        return true;
    }

    private function duplicateCodeBase(){

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
