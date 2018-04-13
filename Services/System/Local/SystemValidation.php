<?php

namespace BlaubandTSG\Services\System\Local;

use BlaubandTSG\Exceptions\EMailAddressInvalidException;
use BlaubandTSG\Exceptions\SystemFileSystemException;
use BlaubandTSG\Exceptions\SystemNotFoundException;
use BlaubandTSG\Exceptions\SystemNotReadyException;
use BlaubandTSG\Exceptions\SystemDBException;
use BlaubandTSG\Exceptions\SystemDBAlreadyExists;
use BlaubandTSG\Exceptions\SystemNameException;
use BlaubandTSG\Exceptions\SystemProcessException;
use BlaubandTSG\Services\System\SystemService;
use Doctrine\Common\Collections\Criteria;
use Shopware\Components\Model\ModelManager;
use BlaubandTSG\Models\System;
use Doctrine\DBAL\Connection;

class SystemValidation
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    /**
     * @var ModelManager
     */
    private $modelManager;

    private $systemNameBlackList = [
        'bin',
        'custom',
        'engine',
        'files',
        'media',
        'recovery',
        'scripts',
        'statistik',
        'themes',
        'var',
        'vendor',
        'web'
    ];

    private $maxProcess = 1;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, ModelManager $modelManager)
    {
        $this->snippets = $snippets;
        $this->modelManager = $modelManager;
    }

    public function validateCurrentProcesses(){

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->neq('state', SystemService::SYSTEM_STATE_READY));
        $repository = $this->modelManager->getRepository(System::class);
        $runningSystems = $repository->matching($criteria);

        if(count($runningSystems) >= $this->maxProcess){
            throw new SystemProcessException(
                $this->snippets->getNamespace('blauband/tsg')->get('tooManyProcesses')
            );
        }
    }

    public function validateSystemName($name)
    {
        //Überprüfen ob der Name valide ist
        preg_match('/^[a-zA-Z][0-9a-zA-Z\s_-]{1,50}$/', $name, $matches);

        if (empty($matches)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blauband/tsg')->get('invalidName')
            );
        }

        if (in_array($name, $this->systemNameBlackList)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blauband/tsg')->get('blackListedName')
            );
        }

        //Überprüfen ob der Name bereits vergeben ist
        $repository = $this->modelManager->getRepository(System::class);
        $system = $repository->findOneBy(['name' => $name]);

        if (!empty($system)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blauband/tsg')->get('nameAlreadyInUse')
            );
        }

        return true;
    }

    public function validateEmailAddress($eMailAddress, $exceptNull = false){
        if(empty($eMailAddress) && $exceptNull){
            return true;
        }

        if(empty($eMailAddress)){
            throw new EMailAddressInvalidException(
                $this->snippets->getNamespace('blauband/tsg')->get('invalidEMailAddress')
            );
        }

        if (!filter_var($eMailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new EMailAddressInvalidException(
                $this->snippets->getNamespace('blauband/tsg')->get('invalidEMailAddress')
            );
        }
    }

    public function validateDBData(Connection $hostConnection, Connection $guestConnection, $dbName, $overwrite = false)
    {
        try {
            $exists = $guestConnection->fetchAll("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");

            if(
                $hostConnection->getDatabase() == $dbName &&
                $hostConnection->getHost() == $guestConnection->getHost()
            ){
                throw new SystemDBException(
                    $this->snippets->getNamespace('blauband/tsg')->get('identicalDB')
                );
            }

            if (!empty($exists)) {
                $isEmpty = $guestConnection->fetchColumn("SELECT COUNT(DISTINCT `table_name`) AS count FROM `information_schema`.`columns` WHERE `table_schema` = '$dbName'");
            }else{
                $isEmpty = '0';
            }
        } catch (\Exception $e) {
            throw new SystemDBException($e->getMessage());
        }


        if ($isEmpty !== '0' && !$overwrite) {
            throw new SystemDBAlreadyExists(
                $this->snippets->getNamespace('blauband/tsg')->get('dbAlreadyExists')
            );
        }

        return true;
    }

    public function validatePath($path)
    {
        $rootPath = substr($path, 0, strrpos($path, '/'));

        if (is_dir($path)) {
            throw new SystemFileSystemException(
                sprintf($this->snippets->getNamespace('blauband/tsg')->get('pathExists'), $path)
            );
        }

        if (!is_writable($rootPath)) {
            throw new SystemFileSystemException(
                sprintf($this->snippets->getNamespace('blauband/tsg')->get('pathNotWritable'), $path)
            );
        }
    }

    public function validateDeleting(System $system){
        if($system == null){
            throw new SystemNotFoundException(
                $this->snippets->getNamespace('blauband/tsg')->get('systemNotFound')
            );
        }

        if(
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_GUEST_DB ||
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_GUEST_CODEBASE ||
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_HOST_DB_ENTRY
        ){
            throw new SystemNotReadyException(
                $this->snippets->getNamespace('blauband/tsg')->get('systemNotDeletable')
            );
        }
    }
}