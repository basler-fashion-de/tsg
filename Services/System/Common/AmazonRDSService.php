<?php

namespace BlaubandOneClickSystem\Services\System\Common;

use BlaubandOneClickSystem\Exceptions\SystemDBException;

//Verschlüsseln
class AmazonRDSService
{
    public $host = 'blauband-mysql-server.cbhzq2g3ehbv.eu-central-1.rds.amazonaws.com';

    private $user = 'root';

    private $password = 'sRZGgK6CZVyd3x3P';

    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    private $connectionService;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, DBConnectionService $connectionService)
    {
        $this->snippets = $snippets;
        $this->connectionService = $connectionService;
    }

    public function createConnection($dbUser, $dbPass, $dbName){
        try{
            $rootConnection = $this->connectionService->createConnection($this->host, $this->user, $this->password);
        }catch (Exception $e){
            throw new SystemDBException(
                $this->snippets->getNamespace('blauband/ocs')->get('unableToConnectToAmazonRds')
            );
        }

        try{
            $rootConnection->exec("CREATE DATABASE `$dbName` DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;");
        }catch (Exception $e){
            throw new SystemDBException(
                sprintf($this->snippets->getNamespace('blauband/ocs')->get('unableToCreateDatabase'), $dbName)
            );
        }

        try{
            $rootConnection->exec("CREATE USER '$dbUser'@'%' IDENTIFIED BY '$dbPass'");
            $rootConnection->exec("GRANT ALL ON $dbName.* TO '$dbUser'@'%'");
            $rootConnection->exec("FLUSH PRIVILEGES");
        }catch (Exception $e){
            throw new SystemDBException(
                sprintf($this->snippets->getNamespace('blauband/ocs')->get('unableToCreateDatabase'), $dbName)
            );
        }

        $rootConnection->close();

        return $this->connectionService->createConnection($this->host, $dbUser, $dbPass);
    }

    public function getUniqDatabaseName(){
        return date("ymd").'_'.substr(uniqid('', false), -4);
    }
}