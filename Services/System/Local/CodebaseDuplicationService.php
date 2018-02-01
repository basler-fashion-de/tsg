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

    private $blackList = [
        '.svn', //SVN
        '.git', //Git
        '.idea' //PHPStorm
    ];

    public function __construct(\Enlight_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    public function duplicateCodeBase($sourcePath, $destinationPath, $exceptions = [])
    {
        $exceptions = array_merge($this->blackList, $exceptions);

        if (!@mkdir($destinationPath) && !is_dir($destinationPath)) {
            throw new SystemFileSystemException(
                sprintf($this->snippets->getNamespace('blauband/ocs')->get('destinationPathNotCreated'), $destinationPath)
            );
        }

        $this->copyWithTar($sourcePath, $destinationPath, $exceptions);
        //$this->copyWithFileSystem($sourcePath, $destinationPath, $exceptions);

        return true;
    }

    public function removeDuplicatedCodebase($path){
        exec("rm -r -d -f $path", $output, $return);
    }

    private function copyWithTar($sourcePath, $destinationPath, $exceptions){
        if(!empty($exceptions)){
            $exceptionsString = ' --exclude='.implode(' --exclude=', $exceptions);
            $exceptionsString = str_replace($sourcePath.'/', '', $exceptionsString);
        }else{
            $exceptionsString = '';
        }

        $command = "tar cf - $exceptionsString . | (cd $destinationPath && tar xvf - )";
        exec($command);
        exec("chmod 0755 $destinationPath");
    }

    private function copyWithFileSystem($sourcePath, $destinationPath, $exceptions){
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
    }
}