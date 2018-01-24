<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use BlaubandOneClickSystem\Exceptions\SystemFileSystemException;
use BlaubandOneClickSystem\Exceptions\SystemNotFoundException;
use BlaubandOneClickSystem\Exceptions\SystemNotReadyException;
use BlaubandOneClickSystem\Exceptions\SystemDBException;
use BlaubandOneClickSystem\Exceptions\SystemNameException;
use BlaubandOneClickSystem\Exceptions\SystemProcessException;
use BlaubandOneClickSystem\Services\SystemService;
use Doctrine\Common\Collections\Criteria;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System;
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
                $this->snippets->getNamespace('blauband/ocs')->get('tooManyProcesses')
            );
        }
    }

    public function validateSystemName($name)
    {
        //Überprüfen ob der Name valide ist
        preg_match('/^[a-zA-Z][0-9a-zA-Z\s_-]{1,50}$/', $name, $matches);

        if (empty($matches)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blauband/ocs')->get('invalidName')
            );
        }

        if (in_array($name, $this->systemNameBlackList)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blauband/ocs')->get('blackListedName')
            );
        }

        //Überprüfen ob der Name bereits vergeben ist
        $repository = $this->modelManager->getRepository(System::class);
        $system = $repository->findOneBy(['name' => $name]);

        if (!empty($system)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blauband/ocs')->get('nameAlreadyInUse')
            );
        }

        return true;
    }

    public function validateDBData(Connection $hostConnection, Connection $guestConnection, $dbName, $overwrite = false)
    {
        try {
            $userName = $guestConnection->getUsername();
            $exists = $guestConnection->fetchAll("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");

            if(
                $hostConnection->getDatabase() == $guestConnection->getDatabase() &&
                $hostConnection->getHost() == $guestConnection->getHost()
            ){
                throw new SystemDBException(
                    $this->snippets->getNamespace('blauband/ocs')->get('identicalDB')
                );
            }

            try {
                $grants = $guestConnection->fetchAll("SELECT Create_priv FROM mysql.user WHERE user = '$userName'");
            } catch (\Exception $e) {
                throw new SystemDBException(
                    $this->snippets->getNamespace('blauband/ocs')->get('missingDBGrants')
                );
            }

            if (!empty($exists)) {
                $isEmpty = $guestConnection->fetchAll("SELECT COUNT(DISTINCT `table_name`) FROM `information_schema`.`columns` WHERE `table_schema` = '$dbName'");

                if (!empty($isEmpty) && !$overwrite) {
                    throw new SystemDBException(
                        $this->snippets->getNamespace('blauband/ocs')->get('dbAlreadyExists')
                    );
                }
            } else {
                if (empty($grants) || $grants[0]['Create_priv'] !== "Y") {
                    throw new SystemDBException(
                        $this->snippets->getNamespace('blauband/ocs')->get('missingDBGrantsCreate')
                    );
                }
            }
        } catch (\Exception $e) {
            throw new SystemDBException($e->getMessage());
        }

        return true;
    }

    public function validatePath($path)
    {
        $rootPath = substr($path, 0, strrpos($path, '/'));

        if (is_dir($path)) {
            throw new SystemFileSystemException(
                sprintf($this->snippets->getNamespace('blauband/ocs')->get('pathExists'), $path)
            );
        }

        if (!is_writable($rootPath)) {
            throw new SystemFileSystemException(
                sprintf($this->snippets->getNamespace('blauband/ocs')->get('pathNotWritable'), $path)
            );
        }
    }

    public function validateDeleting(System $system){
        if($system == null){
            throw new SystemNotFoundException(
                $this->snippets->getNamespace('blauband/ocs')->get('systemNotFound')
            );
        }

        if(
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_GUEST_DB ||
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_GUEST_CODEBASE ||
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_HOST_DB_ENTRY
        ){
            throw new SystemNotReadyException(
                $this->snippets->getNamespace('blauband/ocs')->get('systemNotDeletable')
            );
        }
    }
}