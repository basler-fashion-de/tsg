<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use BlaubandOneClickSystem\Exceptions\SystemFileSystemException;
use BlaubandOneClickSystem\Models\System;

class MailService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    public function preventMail(System $system)
    {
        $mailPath = $system->getPath() . '/mail';
        if (!@mkdir($mailPath) && !is_dir($mailPath)) {
            throw new SystemFileSystemException(
                sprintf($this->snippets->getNamespace('blauband/ocs')->get('pathCanNotCreate'), $mailPath)
            );
        }

        $configPath = $system->getPath() . "/config.php";
        $config = include $configPath;
        $config['mail']['type'] = 'file';
        $config['mail']['path'] = $mailPath;
        file_put_contents($configPath, "<?php\n\n return " . var_export($config, true) . ";");

        return true;
    }


}