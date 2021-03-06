<?php

namespace BlaubandTSG\Services\FolderCompare;

use BlaubandTSG\Exceptions\MissingFolderException;
use BlaubandTSG\Exceptions\MissingParameterException;
use BlaubandTSG\Services\ConfigService;

class FolderCompareService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    private $blackList;

    public $skipHidden;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, ConfigService $filesConfigService)
    {
        $this->snippets = $snippets;
        $this->blackList = $filesConfigService->get('files.blacklist.file', true);
        $this->skipHidden = (
            isset($filesConfigService->get('files.blacklist.@attributes')['hiddenFiles']) &&
            $filesConfigService->get('files.blacklist.@attributes')['hiddenFiles'] === 'true'
        );
    }

    public function compareFolder(array $sourcePaths, array $destinationPaths)
    {
        $list = [];
        $return = new FolderCompareResult();

        $this->validation($sourcePaths, $destinationPaths);

        foreach ($sourcePaths as $sourcePath) {
            if ($this->skipHidden) {
                $sourceIterator = new \RecursiveIteratorIterator(
                    new HiddenFilesAndFolderFilterIterator(
                        new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS)
                    )
                );
            } else {
                $sourceIterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
            }

            foreach ($sourceIterator as $filename) {
                if (in_array($filename->getFilename(), $this->blackList, false)) {
                    continue;
                }

                $list[str_replace($sourcePath, '', $filename)]['left'] = [
                    'path' => $filename,
                    'size' => filesize($filename),
                    'hash' => hash_file("adler32", $filename, FALSE)
                ];
            }
        }

        foreach ($destinationPaths as $destinationPath) {
            if ($this->skipHidden) {
                $destinationIterator = new \RecursiveIteratorIterator(
                    new HiddenFilesAndFolderFilterIterator(
                        new \RecursiveDirectoryIterator($destinationPath, \RecursiveDirectoryIterator::SKIP_DOTS)
                    )
                );
            } else {
                $destinationIterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($destinationPath, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
            }

            foreach ($destinationIterator as $filename) {
                if (in_array($filename->getFilename(), $this->blackList, false)) {
                    continue;
                }

                $list[str_replace($destinationPath, '', $filename)]['right'] = [
                    'path' => $filename,
                    'size' => filesize($filename),
                    'hash' => hash_file("adler32", $filename, FALSE)
                ];
            }
        }

        foreach ($list as $l) {
            $return->addFile($l['left'], $l['right']);
        }

        return $return;
    }

    private function validation(array $sourcePaths, array $destinationPaths)
    {
        //Sind die Parameter vorhanden
        if (empty($sourcePaths) || empty($destinationPaths)) {
            throw new MissingParameterException(
                $this->snippets->getNamespace('blauband/tsg')->get('missingParameter')
            );
        }

        //Existiert der Pfad
        foreach ($sourcePaths as $sourcePath) {
            if (!is_dir($sourcePath)) {
                throw new MissingFolderException(
                    sprintf($this->snippets->getNamespace('blauband/tsg')->get('pathNotFound'), $sourcePath)
                );
            }
        }

        //Existiert der Pfad
        foreach ($destinationPaths as $destinationPath) {
            if (!is_dir($destinationPath)) {
                throw new MissingFolderException(
                    sprintf($this->snippets->getNamespace('blauband/tsg')->get('pathNotFound'), $destinationPath)
                );
            }
        }
    }
}

class HiddenFilesAndFolderFilterIterator extends \RecursiveFilterIterator
{
    public function __construct(RecursiveDirectoryIterator $iterator)
    {
        parent::__construct($iterator);
    }

    public function accept()
    {
        $current = $this->getBasename();
        return strlen($current) && $current[0] !== ".";
    }

    public function hasChildren()
    {
        return parent::hasChildren() && $this->accept();
    }
}