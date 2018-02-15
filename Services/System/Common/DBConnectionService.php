<?php

namespace BlaubandOneClickSystem\Services\System\Common;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use BlaubandOneClickSystem\Exceptions\SystemDBException;

//Verschlüsseln
class DBConnectionService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    public function createConnection($dbHost, $dbUser, $dbPass, $dbName = null){
        try{
            $connection = new Connection(['host' => $dbHost, 'user' => $dbUser, 'password' => $dbPass], new Driver());

            if(!empty($dbName)){
                $connection->exec("USE `$dbName`");
            }
        }catch (\Exception $e){
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/ocs')->get('unableToConnect')
            );
        }

        return $connection;
    }
}