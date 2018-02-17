<?php

namespace BlaubandOneClickSystem\Services\FolderCompare;

use BlaubandOneClickSystem\Exceptions\MissingFolderException;
use BlaubandOneClickSystem\Exceptions\MissingParameterException;
use BlaubandOneClickSystem\Services\ConfigService;

class FolderCompareService
{
    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippets;

    private $blackList;

    public function __construct(\Enlight_Components_Snippet_Manager $snippets, ConfigService $filesConfigService)
    {
        $this->snippets = $snippets;
        $this->blackList = $filesConfigService->get('files.blacklist.file');
    }

    public function compareFolder(array $sourcePaths, array $destinationPaths)
    {
        $list = [];
        $return = new FolderCompareResult();

        $this->validation($sourcePaths, $destinationPaths);

        foreach ($sourcePaths as $sourcePath) {

            foreach (new \RecursiveIteratorIterator(
                         new HiddenFilesAndFolderFilterIterator(
                             new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS)
                         )
                     )
                     as $filename) {
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
            foreach (new \RecursiveIteratorIterator(
                         new HiddenFilesAndFolderFilterIterator(
                             new \RecursiveDirectoryIterator($destinationPath, \RecursiveDirectoryIterator::SKIP_DOTS)
                         )
                     )
                     as $filename) {
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
                $this->snippets->getNamespace('blauband/ocs')->get('missingParameter')
            );
        }

        //Existiert der Pfad
        foreach ($sourcePaths as $sourcePath) {
            if (!is_dir($sourcePath)) {
                throw new MissingFolderException(
                    sprintf($this->snippets->getNamespace('blauband/ocs')->get('pathNotFound'), $sourcePath)
                );
            }
        }

        //Existiert der Pfad
        foreach ($destinationPaths as $destinationPath) {
            if (!is_dir($destinationPath)) {
                throw new MissingFolderException(
                    sprintf($this->snippets->getNamespace('blauband/ocs')->get('pathNotFound'), $destinationPath)
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