<?php

namespace BlaubandOneClickSystem\Services\System\Local;

use BlaubandOneClickSystem\Exceptions\SystemFileSystemException;
use Symfony\Component\Filesystem\Filesystem;

class CodebaseDuplicationService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    public function duplicateCodeBase($sourcePath, $destinationPath, $exceptions = [])
    {
        if (!@mkdir($destinationPath) && !is_dir($destinationPath)) {
            throw new SystemFileSystemException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('destinationPathNotCreated', "Der Pfad [$destinationPath] konnte nicht erstellt werden. Überprüfen Sie die Berechtigungen")
            );
        }

        $fileSystem = new Filesystem();

        $directoryIterator = new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);
        /** @var SplFileInfo $item */
        foreach ($iterator as $item)
        {
            if(strpos($item->getPathname(), $destinationPath) !== false){
                continue;
            }

            foreach ($exceptions as $exception){
                if(strpos($item->getPathname(), $exception) !== false){
                    continue 2;
                }
            }

            if ($item->isDir())
            {
                $fileSystem->mkdir($destinationPath . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
            else
            {
                $fileSystem->copy($item, $destinationPath . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

        return true;
    }

    public function removeDuplicatedCodebase($path){
        $directoryIterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $item)
        {
            $todo = ($item->isDir() ? 'rmdir' : 'unlink');
            $todo($item->getRealPath());
        }

        rmdir($path);
    }
}