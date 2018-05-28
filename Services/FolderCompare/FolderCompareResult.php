<?php

namespace BlaubandTSG\Services\FolderCompare;

class FolderCompareResult
{
    private $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function addFile($left, $right)
    {
        $l = $left;
        $r = $right;
        unset($l['path']);
        unset($r['path']);


        if ($l === $r) {
            $this->data[] = new FolderCompareResultItem($left, $right, FolderCompareResultItem::IDENTICAL);
            return;
        }

        if (empty($l) || empty($r)) {
            $this->data[] = new FolderCompareResultItem($left, $right, FolderCompareResultItem::INSERT);
            return;
        }

        if ($l !== $r) {
            $this->data[] = new FolderCompareResultItem($left, $right, FolderCompareResultItem::CHANGED);
            return;
        }
    }

    public function __toArray()
    {
        $return = [];

        /** @var FolderCompareResultItem $d */
        foreach ($this->data AS $d) {
            $return[] = $d->__toArray();
        }

        return $return;
    }
}

require_once(__DIR__ . '/../../Library/class.Diff.php');

class FolderCompareResultItem
{
    const INSERT = 1;
    const IDENTICAL = 2;
    const CHANGED = 3;

    const MAX_FILE_SIZE = 1024 * 1024 * 2; //2MB

    public $left;

    public $right;

    public $state;

    public $maxSize = false;

    public $title;

    public function __construct($left, $right, $state)
    {
        $this->left = $left;
        $this->right = $right;
        $this->state = $state;

        if ($left['size'] > self::MAX_FILE_SIZE || $right['size'] > self::MAX_FILE_SIZE) {
            $this->html = '';
            $this->maxSize = true;
        } elseif ($state !== self::IDENTICAL) {
            $this->html = \Diff::toTable(\Diff::compareFiles($left['path'], $right['path']));
            $this->html = str_replace('   ', '&nbsp;&nbsp;&nbsp;&nbsp;', $this->html);
            $this->html = str_replace('\t', '&nbsp;&nbsp;&nbsp;&nbsp;', $this->html);
        } else {
            $this->html = '';
        }

        $this->title = empty($this->left) ? $this->right['path'] : $this->left['path'];
        $this->title = str_replace(Shopware()->Container()->getParameter('kernel.root_dir'), '', $this->title);
    }

    public function __toArray()
    {
        return [
            'title' => $this->title,
            'left' => $this->left,
            'right' => $this->right,
            'state' => $this->state,
            'html' => $this->html,
            'maxSize' => $this->maxSize
        ];
    }
}