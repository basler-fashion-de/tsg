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

    public function duplicateCodeBase($sourcePath, $destinationPath)
    {
        if (!@mkdir($destinationPath) && !is_dir($destinationPath)) {
            throw new SystemFileSystemException(
                $this->snippets->getNamespace('blaubandOneClickSystem')->get('destinationPathNotCreated', "Der Pfad [$destinationPath] konnte nicht erstellt werden. Überprüfen Sie die Berechtigungen")
            );
        }

        $fileSystem = new Filesystem();

        $directoryIterator = new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item)
        {
            if(strpos($item->getPathname(), $destinationPath) !== false){
                continue;
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

        //$fileFolderList = scandir($sourcePath, SCANDIR_SORT_ASCENDING);
        /*foreach ($fileFolderList as $fileFolder){
            $sourceFileFolder = $sourcePath.'/'.$fileFolder;
            $destinationFileFolder = $destinationPath.'/'.$fileFolder;
            if(
                $fileFolder != '.' &&
                $fileFolder != '..' &&
                $sourceFileFolder != $destinationPath
            ){

                if(!copy($sourceFileFolder, $destinationFileFolder)){
                    @rmdir($destinationPath);

                    throw new SystemFileSystemException(
                        $this->snippets->getNamespace('blaubandOneClickSystem')->get('destinationFileNotCreated', "Der Pfad/Datei [$destinationFileFolder] konnte nicht erstellt werden. Überprüfen Sie die Berechtigungen")
                    );
                }
            }
        }*/

        return true;
    }
}