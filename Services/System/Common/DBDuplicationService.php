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
    private $dumpPrefix = "BlaubandTSGDBDump-";

    private $mysqlCommands = [
        'mysql',
        '/usr/local/mysql5/bin/mysql' //Profihost
    ];

    private $mysqlDumpCommands = [
        'mysqldump',
        '/usr/local/mysql5/bin/mysqldump' //Profihost
    ];

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, Logger $pluginLogger, $pluginPath)
    {
        $this->snippets = $snippets;
        $this->pluginLogger = $pluginLogger;
        $this->pluginPath = $pluginPath;
    }

    public function createDatabaseAndUse(Connection $connection, $dbName)
    {
        try {
            $exists = $connection->fetchAll("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");
            $this->pluginLogger->addInfo('Blauband TSG: Database ' . $dbName . (empty($exists) ? ' dosnt exists' : ' exists'));

            if (empty($exists)) {
                $createResult = $connection->exec("CREATE DATABASE `$dbName` DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;");
                $this->pluginLogger->addInfo('Blauband TSG: Database ' . $dbName . ' created with result (' . $createResult . ')');
            }
        } catch (\Exception $e) {
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/tsg')->get('missingDBGrantsCreate')
            );
        }

        try {
            $connection->exec("USE `$dbName`");
        } catch (\Exception $e) {
            throw new SystemDBException($e->getMessage());
        }
    }

    public function duplicateData(Connection $sourceConnection, Connection $destinationConnection, array $tables = [])
    {
        if (empty($tables)) {
            $tables = $sourceConnection->fetchAll("SHOW TABLES FROM `" . $sourceConnection->getDatabase() . '`');
            $tables = array_map(function ($i) {
                return array_shift($i);
            }, $tables);
        }

        foreach ($tables as $table) {
            $this->duplicateTable($table, $sourceConnection, $destinationConnection);
        }

        return true;
    }

    private function duplicateTable($table, Connection $sourceConnection, Connection $destinationConnection)
    {
        if ($table == 'magnalister_ebay_prepare' ||
            $table == 'magnalister_cdiscount_prepare'
        ) {
            return;
        }


        $sourceHost = $sourceConnection->getHost();
        $sourcePort = $sourceConnection->getPort();
        $sourceUser = $sourceConnection->getUsername();
        $sourcePass = $sourceConnection->getPassword();
        $sourceDb = $sourceConnection->getDatabase();

        $dumpName = uniqid($this->dumpPrefix, false) . "-$table.sql";
        $dumpPath = $this->pluginPath . '/' . $dumpName;
        $passString = empty($sourcePass) ? '' : "--password='$sourcePass'";

        if(!$dumpCommand = $this->findCommand($this->mysqlDumpCommands)){
            throw new SystemDBException("No dump command found");
        }

        $exportCommand = "$dumpCommand -h$sourceHost -P$sourcePort -u$sourceUser $passString --default-character-set=utf8 $sourceDb $table > $dumpPath";
        $output = shell_exec($exportCommand);

        if ($output !== null) {
            @unlink($dumpName);
            throw new SystemDBException($output);
        }

        $destinationHost = $destinationConnection->getHost();
        $destinationPort = $destinationConnection->getPort();
        $destinationUser = $destinationConnection->getUsername();
        $destinationPass = $destinationConnection->getPassword();
        $destinationDb = $destinationConnection->getDatabase();
        $passString = empty($destinationPass) ? '' : "--password='$destinationPass'";

        if(!$mysqlCommand = $this->findCommand($this->mysqlCommands)){
            throw new SystemDBException("No mysql command found");
        }

        $importCommand = "$mysqlCommand -h$destinationHost -P$destinationPort -u$destinationUser $passString $destinationDb < $dumpPath";
        $output = shell_exec($importCommand);
        $this->pluginLogger->addInfo("Blauband TSG: Table '$table' duplicated");

        if ($output !== null) {
            @unlink($dumpName);
            throw new SystemDBException($output);
        }

        @unlink($dumpPath);
    }

    private function findCommand($list)
    {
        foreach ($list as $cmd) {
            if (!empty(shell_exec("which $cmd"))) {
                return $cmd;
            }
        }

        return false;
    }
}