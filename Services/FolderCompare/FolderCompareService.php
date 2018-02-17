<?php

namespace BlaubandOneClickSystem\Services\FolderCompare;

class FolderCompareService
{
    public function compareFolder($sourcePath, $destinationPath)
    {
        $list = [];
        $return = new FolderCompareResult();

        foreach (new \RecursiveIteratorIterator(
                     new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS))
                 as $filename) {
            $list[str_replace($sourcePath, '', $filename)]['left'] = [
                'path' => $filename,
                'size' => filesize($filename),
                'hash' => hash_file("adler32", $filename, FALSE)
            ];
        }

        foreach (new \RecursiveIteratorIterator(
                     new \RecursiveDirectoryIterator($destinationPath, \RecursiveDirectoryIterator::SKIP_DOTS))
                 as $filename) {
            $list[str_replace($destinationPath, '', $filename)]['right'] = [
                'path' => $filename,
                'size' => filesize($filename),
                'hash' => hash_file("adler32", $filename, FALSE)
            ];
        }

        foreach ($list as $l){
            $return->addFile($l['left'], $l['right']);
        }

        return $return;
    }
}