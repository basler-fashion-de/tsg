<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use BlaubandOneClickSystem\Exceptions\SystemFileSystemException;
use BlaubandOneClickSystem\Exceptions\SystemNotFoundException;
use BlaubandOneClickSystem\Exceptions\SystemNotReadyException;
use BlaubandOneClickSystem\Exceptions\SystemDBException;
use BlaubandOneClickSystem\Exceptions\SystemNameException;
use BlaubandOneClickSystem\Exceptions\SystemProcessException;
use BlaubandOneClickSystem\Services\System\SystemService;
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
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('tooManyProcesses', 'Es laufen bereits die maximale Anzahl der Prozesse. Bitte warten Sie bis mind. einer fertig ist.')
            );
        }
    }

    public function validateSystemName($name)
    {
        //Überprüfen ob der Name valide ist
        preg_match('/^[a-zA-Z][0-9a-zA-Z\s_-]{1,50}$/', $name, $matches);

        if (empty($matches)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('invalidName', 'Dieser Name ist invalide, bitte starten Sie Ihren Namen mit einem Buchstaben und verwenden nur große und kleine Buchstaben, Zahlen und die Zeichen \'-\', \'_\'. ')
            );
        }

        if (in_array($name, $this->systemNameBlackList)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('blackListedName', 'Dieser Name kann leider nicht verwendet werden. Bitte verwendet Sie einen anderen.')
            );
        }

        //Überprüfen ob der Name bereits vergeben ist
        $repository = $this->modelManager->getRepository(System::class);
        $system = $repository->findOneBy(['name' => $name]);

        if (!empty($system)) {
            throw new SystemNameException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('nameAlreadyInUse', 'Dieser Name wird bereits verwendet. Bitte wählen Sie einen anderen.')
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
                    $this->snippets->getNamespace('blaubandOneClickSystem')->get('identicalDB', 'Der angegebene Datenbanken sind identisch.')
                );
            }

            try {
                $grants = $guestConnection->fetchAll("SELECT Create_priv FROM mysql.user WHERE user = '$userName'");
            } catch (\Exception $e) {
                throw new SystemDBException(
                    $this->snippets->getNamespace('blaubandOneClickSystem')->get('missingDBGrants', 'Der angegebene Datenbank User hat keine ausreichenden Berechtigung.')
                );
            }

            if (!empty($exists)) {
                $isEmpty = $guestConnection->fetchAll("SELECT COUNT(DISTINCT `table_name`) FROM `information_schema`.`columns` WHERE `table_schema` = '$dbName'");

                if (!empty($isEmpty) && !$overwrite) {
                    throw new SystemDBException(
                        $this->snippets->getNamespace('blaubandOneClickSystem')->get('dbAlreadyExists', 'Datenbank besteht bereits und es befinden sich Daten in dieser Datenbank. Bitte stellen Sie sicher dass die Datenbank leer ist.')
                    );
                }
            } else {
                if (empty($grants) || $grants[0]['Create_priv'] !== "Y") {
                    throw new SystemDBException(
                        $this->snippets->getNamespace('blaubandOneClickSystem')->get('missingDBGrantsCreate', 'Der angegebene Datenbank User hat keine ausreichenden Berechtigung um eine Datenbank zu erstellen.')
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
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('pathExists', "Der Pfad [$path] existiert bereits. Bitte wählen Sie ein anderen Systemnamen")
            );
        }

        if (!is_writable($rootPath)) {
            throw new SystemFileSystemException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('pathNotWritable', "Der Pfad [$path] kann nicht erstellt werden. Stellen Sie sicher das alle benötigten Rechte vorhanden sind.")
            );
        }
    }

    public function validateDeleting(System $system){
        if($system == null){
            throw new SystemNotFoundException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('systemNotFound', 'Dieses System wurde nicht gefunden.')
            );
        }

        if(
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_GUEST_DB ||
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_GUEST_CODEBASE ||
            $system->getState() == SystemService::SYSTEM_STATE_DELETING_HOST_DB_ENTRY
        ){
            throw new SystemNotReadyException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('systemNotDeletable', 'Dieses System wird bereits bearbeitet. Löschen ist aktuell nicht möglich.')
            );
        }
    }
}