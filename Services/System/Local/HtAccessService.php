<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use BlaubandOneClickSystem\Exceptions\SystemFileSystemException;

class HtAccessService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    public function createHtPass($path, $user)
    {
        list($accessfile, $accessfileName) = $this->getHtAccessFile($path);
        list($passwdfile, $passwdfileName) = $this->getHtPasswdFile($path);

        $content = "AuthType Basic\nAuthName \"Protected Area\"\nAuthUserFile $passwdfileName\nRequire valid-user\n\n";
        $content .= file_get_contents($accessfileName);

        fwrite($accessfile, $content);
        fclose($accessfile);

        $encryptedUser = [];
        foreach ($user as $username => $password) {
            $encrypted_password = crypt($password, base64_encode($password));
            $encryptedUser[] = $username . ':' . $encrypted_password;
        }

        fwrite($passwdfile, implode('\n', $encryptedUser));
        fclose($passwdfile);

        return true;
    }

    private function getHtPasswdFile($path)
    {
        $file = $path . '/.htpasswd';

        $fp = fopen($file, "w+");
        if (!$fp) {
            throw new SystemFileSystemException(
                sprintf(
                    $this->snippets->getNamespace('blauband/ocs')->get('fileNotFoundOrCreated'),
                    $file
                )
            );
        }

        return [$fp, $file];
    }

    private function getHtAccessFile($path)
    {
        $file = $path . '/.htaccess';

        $fp = fopen($file, "r+");
        if (!$fp) {
            throw new SystemFileSystemException(
                sprintf(
                    $this->snippets->getNamespace('blauband/ocs')->get('fileNotFoundOrCreated'),
                    $file
                ));
        }

        return [$fp, $file];
    }
}