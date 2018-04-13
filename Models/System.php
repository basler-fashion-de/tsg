<?php

namespace BlaubandTSG\Models;

use BlaubandTSG\Services\SystemServiceInterface;
use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="blauband_tsg_systems")
 * @ORM\HasLifecycleCallbacks
 */
class System extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var
     * @ORM\Column(name="name", type="string", unique=true)
     */
    private $name;

    /**
     * @var
     * @ORM\Column(name="type", type="text")
     */
    private $type;

    /**
     * @var
     * @ORM\Column(name="state", type="text")
     */
    private $state;

    /**
     * @var
     * @ORM\Column(name="path", type="string", unique=true)
     */
    private $path;

    /**
     * @var
     * @ORM\Column(name="url", type="string", unique=true)
     */
    private $url;

    /**
     * @var
     * @ORM\Column(name="db_host", type="text")
     */
    private $dbHost;

    /**
     * @var
     * @ORM\Column(name="db_port", type="text", nullable=true)
     */
    private $dbPort;

    /**
     * @var
     * @ORM\Column(name="db_user", type="text")
     */
    private $dbUsername;

    /**
     * @var
     * @ORM\Column(name="db_pass", type="text")
     */
    private $dbPassword;

    /**
     * @var
     * @ORM\Column(name="db_name", type="text")
     */
    private $dbName;

    /**
     * @var
     * @ORM\Column(name="ht_passwd_username", type="text", nullable=true)
     */
    private $htPasswdUsername;

    /**
     * @var
     * @ORM\Column(name="ht_passwd_password", type="text", nullable=true)
     */
    private $htPasswdPassword;

    /**
     * @var
     * @ORM\Column(name="media_folder_duplicated", type="boolean")
     */
    private $mediaFolderDuplicated;

    /**
     * @var
     * @ORM\Column(name="start_parameter", type="text")
     */
    private $startParameter;

    /**
     * @var
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createDate = new \DateTime("now");
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemoveEvent()
    {
        /** @var SystemServiceInterface $service */
        $service = Shopware()->Container()->get("blauband_tsg." . $this->type . "_system_service");
        $service->deleteSystem($this);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @param mixed $dbHost
     */
    public function setDbHost($dbHost)
    {
        $this->dbHost = $dbHost;
    }

    /**
     * @return mixed
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * @param mixed $dbPort
     */
    public function setDbPort($dbPort)
    {
        $this->dbPort = $dbPort;
    }

    /**
     * @return mixed
     */
    public function getDbUsername()
    {
        return $this->dbUsername;
    }

    /**
     * @param mixed $dbUsername
     */
    public function setDbUsername($dbUsername)
    {
        $this->dbUsername = $dbUsername;
    }

    /**
     * @return mixed
     */
    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    /**
     * @param mixed $dbPassword
     */
    public function setDbPassword($dbPassword)
    {
        $this->dbPassword = $dbPassword;
    }

    /**
     * @return mixed
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @param mixed $dbName
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

    /**
     * @return mixed
     */
    public function getHtPasswdUsername()
    {
        return $this->htPasswdUsername;
    }

    /**
     * @param mixed $htPasswdUsername
     */
    public function setHtPasswdUsername($htPasswdUsername)
    {
        $this->htPasswdUsername = $htPasswdUsername;
    }

    /**
     * @return mixed
     */
    public function getHtPasswdPassword()
    {
        return $this->htPasswdPassword;
    }

    /**
     * @param mixed $htPasswdPassword
     */
    public function setHtPasswdPassword($htPasswdPassword)
    {
        $this->htPasswdPassword = $htPasswdPassword;
    }

    /**
     * @return mixed
     */
    public function getMediaFolderDuplicated()
    {
        return $this->mediaFolderDuplicated;
    }

    /**
     * @param mixed $mediaFolderDuplicated
     */
    public function setMediaFolderDuplicated($mediaFolderDuplicated)
    {
        $this->mediaFolderDuplicated = $mediaFolderDuplicated;
    }

    /**
     * @return mixed
     */
    public function getStartParameter()
    {
        return unserialize($this->startParameter);
    }

    /**
     * @param mixed $startParameter
     */
    public function setStartParameter($startParameter)
    {
        $this->startParameter = serialize($startParameter);
    }

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function __toArray(){
        return get_object_vars($this);
    }
}