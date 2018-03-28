<?php

namespace BlaubandOneClickSystem\Services\System\Common;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use BlaubandOneClickSystem\Exceptions\SystemDBException;

class DBConnectionService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    /**
     * @var DBDuplicationService
     */
    private $dbDuplicationService;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, DBDuplicationService $dbDuplicationService)
    {
        $this->snippets = $snippets;
        $this->dbDuplicationService = $dbDuplicationService;
    }

    public function createConnection($dbHost, $dbUser, $dbPass, $dbName = null, $dbPort = 3306, $dbCharset = 'utf8'){
        try{
            $connection = new Connection([
                'host' => $dbHost,
                'user' => $dbUser,
                'password' => $dbPass,
                'port' => $dbPort,
                'charset' => $dbCharset
            ], new Driver());

            if(!empty($dbName)){
                $this->dbDuplicationService->createDatabaseAndUse($connection, $dbName);
            }
        }catch (\Exception $e){
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/ocs')->get('unableToConnect')
            );
        }

        return $connection;
    }
}