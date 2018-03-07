<?php

namespace BlaubandOneClickSystem\Services\System\Common;

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
    private $pluginPath;

    /**
     * @var string
     */
    private $dumpPrefix = "BlaubandOneClickSystemDBDump";

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, $pluginPath)
    {
        $this->snippets = $snippets;
        $this->pluginPath = $pluginPath;
    }

    public function createDatabaseAndUse(Connection $connection, $dbName){
        try{
            $exists = $connection->fetchAll("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");
            
            if (empty($exists)) {
                $connection->exec("CREATE DATABASE `$dbName` DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;");
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

    public function duplicateData(Connection $sourceConnection, Connection $destinationConnection, array $tables = []){
        $sourceHost = $sourceConnection->getHost();
        $sourceUser = $sourceConnection->getUsername();
        $sourcePass = $sourceConnection->getPassword();
        $sourceDb = $sourceConnection->getDatabase();
        $sourceTables = implode(' ', $tables);
        $dumpName = uniqid($this->dumpPrefix, false).".sql";
        $dumpPath = $this->pluginPath.'/'.$dumpName;
        $passString = empty($sourcePass) ? '' : "-p$sourcePass";

        $exportCommand = "mysqldump -h$sourceHost -u$sourceUser $passString --default-character-set=utf8 $sourceDb $sourceTables > $dumpPath";

        $output = shell_exec($exportCommand);

        if($output !== null){
            @unlink($dumpName);
            throw new \SystemDBException($output);
        }

        $destinationHost = $destinationConnection->getHost();
        $destinationUser = $destinationConnection->getUsername();
        $destinationPass = $destinationConnection->getPassword();
        $destinationDb = $destinationConnection->getDatabase();
        $importCommand = "mysql -h$destinationHost -u$destinationUser -p$destinationPass $destinationDb < $dumpPath";
        $output = shell_exec($importCommand);

        if($output !== null){
            @unlink($dumpName);
            throw new \SystemDBException($output);
        }

        @unlink($dumpPath);

        return true;
    }
}