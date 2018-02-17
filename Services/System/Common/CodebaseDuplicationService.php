<?php

namespace BlaubandOneClickSystem\Services\System\Common;

use BlaubandOneClickSystem\Exceptions\SystemFileSystemException;

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

        $command = "(cd $sourcePath && tar cmf - $exceptionsString .) | (cd $destinationPath && tar xvmf - )";
        exec($command);
        exec("chmod 0755 $destinationPath");
    }
}