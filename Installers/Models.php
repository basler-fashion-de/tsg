<?php

namespace BlaubandOneClickSystem\Installers;

use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System AS OCSSystem;

class Models
{
    /**
     * @var ModelManager
     * */
    private $modelManager;

    /**
     * @var array
     */
    private $classes;

    /**
     * @var SchemaTool
     */
    private $tool;

    /**
     * Models constructor.
     *
     * @param ModelManager $modelManager
     */
    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
        $this->tool = new SchemaTool($this->modelManager);
        $this->classes = [$this->modelManager->getClassMetadata(OCSSystem::class)];
    }

    /**
     * @return void
     */
    public function install()
    {
        $this->tool->createSchema($this->classes);
    }

    /**
     * @return void
     */
    public function uninstall()
    {
        $this->tool->dropSchema($this->classes);
    }

    /**
     * @return void
     */
    public function update()
    {
        $this->tool->updateSchema($this->classes, true);
    }
}
