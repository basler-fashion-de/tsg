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

    /**
     * @var string
     */
    private $dumpPrefix = "BlaubandOneClickSystemDBDump";

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    public function createDatabaseAndUse(Connection $connection, $dbName){
        try{
            $exists = $connection->fetchAll("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");
            
            if (empty($exists)) {
                $connection->exec("CREATE DATABASE `$dbName`");
            }
        }catch (\Exception $e){
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/ocs')->get('missingDBGrantsCreate')
            );
        }

        try{
            $connection->exec("USE `$dbName`");
        }catch (\Exception $e){
            throw new SystemDBException($e->getMessage());
        }
    }

    public function duplicateData(Connection $hostConnection, Connection $guestConnection){
        $hostHost = $hostConnection->getHost();
        $hostUser = $hostConnection->getUsername();
        $hostPass = $hostConnection->getPassword();
        $hostDb = $hostConnection->getDatabase();
        $dumpName = uniqid($this->dumpPrefix, false).".sql";
        $exportCommand = "mysqldump -h$hostHost -u$hostUser -p$hostPass $hostDb > $dumpName";
        $output = shell_exec($exportCommand);

        if($output !== null){
            @unlink($dumpName);
            throw new \SystemDBException($output);
        }

        $guestHost = $guestConnection->getHost();
        $guestUser = $guestConnection->getUsername();
        $guestPass = $guestConnection->getPassword();
        $guestDb = $guestConnection->getDatabase();
        $importCommand = "mysql -h$guestHost -u$guestUser -p$guestPass $guestDb < $dumpName";
        $output = shell_exec($importCommand);

        if($output !== null){
            @unlink($dumpName);
            throw new \SystemDBException($output);
        }

        @unlink($dumpName);

        return true;
    }
}