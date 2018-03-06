<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use BlaubandOneClickSystem\Exceptions\SystemFileSystemException;
use BlaubandOneClickSystem\Models\System;

require_once(__DIR__ . '/../../../Library/php-email-parser-master/PlancakeEmailParser.php');

class MailService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    private $docRoot;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, $docRoot)
    {
        $this->snippets = $snippets;
        $this->docRoot = $docRoot;
    }

    public function preventMail($path)
    {
        $mailPath = $path . '/mail';
        if (!@mkdir($mailPath) && !is_dir($mailPath)) {
            throw new SystemFileSystemException(
                sprintf($this->snippets->getNamespace('blauband/ocs')->get('pathCanNotCreate'), $mailPath)
            );
        }

        $configPath = $path . "/config.php";
        $config = include $configPath;
        $config['mail']['type'] = 'file';
        $config['mail']['path'] = $mailPath;
        $configData = "<?php\n\n return " . var_export($config, true) . ";";
        $result = file_put_contents($configPath, $configData);

        if ($result === false) {
            throw new SystemFileSystemException(
                $this->snippets->getNamespace('blauband/ocs')->get('canNoWriteConfigPhp')
            );
        }

        return true;
    }

    public function allowMail($path)
    {
        $configPath = $path . "/config.php";
        $config = include $configPath;
        unset($config['mail']);
        $configData = "<?php\n\n return " . var_export($config, true) . ";";
        $result = file_put_contents($configPath, $configData);

        if ($result === false) {
            throw new SystemFileSystemException(
                $this->snippets->getNamespace('blauband/ocs')->get('canNoWriteConfigPhp')
            );
        }

        return true;
    }

    public function isMailAllowed($path)
    {
        $configPath = $path . "/config.php";
        $config = include $configPath;
        return !(isset($config['mail']['type']) && $config['mail']['type'] == 'file');
    }

    public function loadMails()
    {
        $mailPath = $this->docRoot . '/mail';

        if (!is_dir($mailPath)) {
            return [];
        }

        $mails = array_diff(scandir($mailPath, SCANDIR_SORT_NONE), array('.', '..'));

        foreach ($mails as &$mail) {
            $rawContent = file_get_contents($mailPath . '/' . $mail);
            $mailObject = new \PlancakeEmailParser($rawContent);
            $mailArray['to'] = $mailObject->getTo();
            $mailArray['subject'] = utf8_decode($mailObject->getSubject());
            $mailArray['body'] = quoted_printable_decode($mailObject->getBody(\PlancakeEmailParser::HTML));

            $mailArray['from'] = $mailObject->getHeader('from');

            $mail = $mailArray;
        }

        return $mails;
    }
}