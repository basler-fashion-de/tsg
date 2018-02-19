<?php

namespace BlaubandOneClickSystem\Services\System\Common;

use BlaubandOneClickSystem\Exceptions\SystemFileSystemException;
use BlaubandOneClickSystem\Services\ConfigService;

class CodebaseDuplicationService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    private $blackList;

    private $skipHidden;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, ConfigService $filesConfigService)
    {
        $this->snippets = $snippets;
        $this->blackList = $filesConfigService->get('files.blacklist.file');
        $this->skipHidden = (
            isset($filesConfigService->get('files.blacklist.@attributes')['hiddenFiles']) &&
            $filesConfigService->get('files.blacklist.@attributes')['hiddenFiles'] === 'true'
            );
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
        $exceptionsString = '';

        if(!empty($exceptions)){
            $exceptionsString .= ' --exclude='.implode(' --exclude=', $exceptions);
            $exceptionsString = str_replace($sourcePath.'/', '', $exceptionsString);
        }

        if($this->skipHidden){
            $exceptionsString .= " --exclude='.[^/]*' ";
        }

        $command = "(cd $sourcePath && tar cmf - $exceptionsString .) | (cd $destinationPath && tar xvmf - )";
        exec($command);
        exec("chmod 0755 $destinationPath");
    }
}