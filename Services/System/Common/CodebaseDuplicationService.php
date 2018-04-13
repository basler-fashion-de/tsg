<?php

namespace BlaubandTSG\Services\System\Common;

use BlaubandTSG\Exceptions\SystemFileSystemException;
use BlaubandTSG\Services\ConfigService;

class CodebaseDuplicationService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    private $blackList;

    private $vcsFiles;

    private $skipHidden;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, ConfigService $filesConfigService)
    {
        $this->snippets = $snippets;
        $this->blackList = $filesConfigService->get('files.blacklist.file', true);
        $this->vcsFiles = (
            isset($filesConfigService->get('files.blacklist.@attributes')['vcsFiles']) &&
            $filesConfigService->get('files.blacklist.@attributes')['vcsFiles'] === 'true'
        );

        $this->skipHidden = (
            isset($filesConfigService->get('files.blacklist.@attributes')['hiddenFiles']) &&
            $filesConfigService->get('files.blacklist.@attributes')['hiddenFiles'] === 'true'
        );
    }

    public function duplicateCodeBase($sourcePath, $destinationPath, $exceptions = [])
    {
        if (!@mkdir($destinationPath) && !is_dir($destinationPath)) {
            throw new SystemFileSystemException(
                sprintf($this->snippets->getNamespace('blauband/tsg')->get('destinationPathNotCreated'), $destinationPath)
            );
        }

        $this->copyWithTar($sourcePath, $destinationPath, $exceptions);

        return true;
    }

    public function removeDuplicatedCodebase($path)
    {
        exec("rm -r -d -f $path", $output, $return);
    }

    private function copyWithTar($sourcePath, $destinationPath, $exceptions)
    {
        $exceptions = array_merge($this->blackList, $exceptions);

        $exceptionsString = ' --exclude=' . $destinationPath;

        if (!empty($exceptions)) {
            $exceptionsString .= ' --exclude=' . implode(' --exclude=', $exceptions);
        }

        if ($this->skipHidden) {
            $exceptionsString .= " --exclude='.[^/]*' ";
        }

        if ($this->vcsFiles) {
            $exceptionsString .= ' --exclude-vcs ';
        }

        $exceptionsString = str_replace($sourcePath . '/', '', $exceptionsString);

        $command = "(cd $sourcePath && tar cmf - $exceptionsString .) | (cd $destinationPath && tar xvmf - )";
        exec($command);
        exec("chmod 0755 $destinationPath");
    }
}