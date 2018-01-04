<?php

namespace BlaubandOneClickSystem\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use BlaubandOneClickSystem\Exceptions\SystemDBException;

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

    public function createConnection($dbHost, $dbUser, $dbPass){
        try{
            return new Connection(['host' => $dbHost, 'user' => $dbUser, 'password' => $dbPass], new Driver());
        }catch (\Exception $e){
            throw new \SystemDBException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('unableToConnect', 'Es konnte keine Datenbank verbindung hergestellt werden.')
            );
        }
    }

    public function createDatabaseAndUse(Connection $connection, $dbName){
        try{
            $result = $connection->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");

            if($result === 0){
                throw new \SystemDBException(
                    $this->snippets->getNamespace('blaubandOneClickSystem')->get('unableToCreateDatabase', "Es konnte die Datenbank [$dbName] nicht erstellt werden.")
                );
            }

            $connection->exec("USE `$dbName`");
        }catch (\Exception $e){
            throw new SystemDBException($e->getMessage());
        }
    }

    public function duplicateData(Connection $hostConnection, Connection $guestConnection){
        $hostTableList = $hostConnection->fetchAll("SHOW TABLES FROM ".$hostConnection->getDatabase());
        $hostDBName = $hostConnection->getDatabase();
        $guestDBName = $guestConnection->getDatabase();

        foreach ($hostTableList as $table){
            $tableName = array_pop(array_values($table));
            $guestConnection->exec("DROP TABLE IF EXISTS `$guestDBName`.`$tableName`");
            $guestConnection->exec("CREATE TABLE `$guestDBName`.`$tableName` LIKE `$hostDBName`.`$tableName`");
        }
    }
}