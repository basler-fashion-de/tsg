<?php

namespace BlaubandTSG\Services\System\Common;

use Doctrine\DBAL\Connection;
use BlaubandTSG\Exceptions\SystemDBException;
use Shopware\Components\Logger;

class DBDuplicationService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    /**
     * @var $logger Logger
     */
    private $pluginLogger;

    /**
     * @var string
     */
    private $pluginPath;

    /**
     * @var string
     */
    private $dumpPrefix = "BlaubandTSGDBDump";

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, Logger $pluginLogger, $pluginPath)
    {
        $this->snippets = $snippets;
        $this->pluginLogger = $pluginLogger;
        $this->pluginPath = $pluginPath;
    }

    public function createDatabaseAndUse(Connection $connection, $dbName){
        try{
            $exists = $connection->fetchAll("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");
            $this->pluginLogger->addInfo('Blauband TSG: Database ' . $dbName . (empty($exists) ? ' dosnt exists' : ' exists'));
            
            if (empty($exists)) {
                $createResult = $connection->exec("CREATE DATABASE `$dbName` DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;");
                $this->pluginLogger->addInfo('Blauband TSG: Database ' . $dbName . ' created with result ('.$createResult.')');
            }
        }catch (\Exception $e){
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/tsg')->get('missingDBGrantsCreate')
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
        $sourcePort = $sourceConnection->getPort();
        $sourceUser = $sourceConnection->getUsername();
        $sourcePass = $sourceConnection->getPassword();
        $sourceDb = $sourceConnection->getDatabase();
        $sourceTables = implode(' ', $tables);
        $dumpName = uniqid($this->dumpPrefix, false).".sql";
        $dumpPath = $this->pluginPath.'/'.$dumpName;
        $passString = empty($sourcePass) ? '' : "-p$sourcePass";

        $exportCommand = "mysqldump -h$sourceHost -P$sourcePort -u$sourceUser $passString --default-character-set=utf8 $sourceDb $sourceTables > $dumpPath";
        $this->pluginLogger->addInfo('Blauband TSG: Dumpfile will write with command: '.$exportCommand);

        $output = shell_exec($exportCommand);
        $this->pluginLogger->addInfo('Blauband TSG: Result: '.$output);

        if($output !== null){
            @unlink($dumpName);
            throw new \SystemDBException($output);
        }

        $destinationHost = $destinationConnection->getHost();
        $destinationPort = $destinationConnection->getPort();
        $destinationUser = $destinationConnection->getUsername();
        $destinationPass = $destinationConnection->getPassword();
        $destinationDb = $destinationConnection->getDatabase();
        $passString = empty($destinationPass) ? '' : "-p$destinationPass";

        $importCommand = "mysql -h$destinationHost -P$destinationPort -u$destinationUser $passString $destinationDb < $dumpPath";
        $this->pluginLogger->addInfo('Blauband TSG: Dumpfile will read with command: '.$importCommand);

        $output = shell_exec($importCommand);
        $this->pluginLogger->addInfo('Blauband TSG: Result: '.$output);

        if($output !== null){
            @unlink($dumpName);
            throw new \SystemDBException($output);
        }

        @unlink($dumpPath);

        return true;
    }
}