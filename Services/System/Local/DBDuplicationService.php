<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use Doctrine\DBAL\Connection;
use BlaubandOneClickSystem\Exceptions\SystemDBException;

class DBDuplicationService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
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


            //das muss anders gemacht werden
            $guestConnection->exec("CREATE TABLE `$guestDBName`.`$tableName` LIKE `$hostDBName`.`$tableName`");
        }
    }
}