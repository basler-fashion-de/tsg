<?php

namespace BlaubandOneClickSystem\Services\FolderCompare;

class FolderCompareResult
{
    private $data;

    private $state;

    public function __construct()
    {
        $this->data = [];
        $this->state = FolderCompareResultItem::IDENTICAL;
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

    public $left;

    public $right;

    public $state;

    public function __construct($left, $right, $state)
    {
        $this->left = $left;
        $this->right = $right;
        $this->state = $state;

        if ($state !== self::IDENTICAL) {
            $this->html = \Diff::toTable(\Diff::compareFiles($left['path'], $right['path']));
            $this->html = str_replace('   ','&nbsp;&nbsp;&nbsp;&nbsp;',$this->html);
            $this->html = str_replace('\t','&nbsp;&nbsp;&nbsp;&nbsp;',$this->html);
        } else {
            $this->html = '';
        }
    }

    public function __toArray()
    {
        return [
            'left' => $this->left,
            'right' => $this->right,
            'state' => $this->state,
            'html' => $this->html
        ];
    }
}