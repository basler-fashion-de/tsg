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
    private $dumpName = "BlaubandOneClickSystemDBDump.sql";

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
        $hostUser = $hostConnection->getUsername();
        $hostPass = $hostConnection->getPassword();
        $hostDb = $hostConnection->getDatabase();
        $exportCommand = "mysqldump -u$hostUser -p$hostPass $hostDb > $this->dumpName";
        $output = shell_exec($exportCommand);

        if($output !== null){
            @unlink($this->dumpName);
            throw new \SystemDBException($output);
        }

        $guestUser = $guestConnection->getUsername();
        $guestPass = $guestConnection->getPassword();
        $guestDb = $guestConnection->getDatabase();
        $importCommand = "mysql -u$guestUser -p$guestPass $guestDb < $this->dumpName";
        $output = shell_exec($importCommand);

        if($output !== null){
            @unlink($this->dumpName);
            throw new \SystemDBException($output);
        }

        @unlink($this->dumpName);

        return true;
    }
}