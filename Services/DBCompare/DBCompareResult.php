<?php

namespace BlaubandTSG\Services\DBCompare;

class DBCompareResult
{
    private $data;

    private $header;

    private $diffs;

    private $table;

    private $state;

    public function __construct($table = null)
    {
        $this->data = [];
        $this->header = [];
        $this->diffs = [];
        $this->table = $table;
        $this->state = DBCompareResultItem::IDENTICAL;
    }

    public function addRow($left, $right)
    {
        if (!empty($left)) {
            $this->header = array_unique(array_merge(array_keys($left), $this->header));
        }

        if (!empty($right)) {
            $this->header = array_unique(array_merge(array_keys($right), $this->header));
        }

        if ($left === $right) {
            $this->data[] = new DBCompareResultItem($left, $right, DBCompareResultItem::IDENTICAL);
            return;
        }

        if (empty($left) || empty($right)) {
            $this->data[] = new DBCompareResultItem($left, $right, DBCompareResultItem::INSERT);
            $this->diffs = array_combine($this->header, $this->header);
            $this->state = DBCompareResultItem::CHANGED;

            return;
        }

        if ($left !== $right) {
            $this->data[] = new DBCompareResultItem($left, $right, DBCompareResultItem::CHANGED);
            $this->state = DBCompareResultItem::CHANGED;

            foreach ($this->header as $h) {
                if ($left[$h] !== $right[$h]) {
                    $this->diffs[$h] = $h;
                }
            }

            return;
        }
    }

    public function __toArray()
    {
        $return = [];

        /** @var DBCompareResultItem $d */
        foreach ($this->data AS $d) {
            $return[] = [
                'left' => $d->left,
                'right' => $d->right,
                'state' => $d->state
            ];
        }

        return [
            'data' => $return,
            'header' => array_filter($this->header),
            'diffs' => $this->diffs,
            'table' => $this->table,
            'empty_table' => empty($this->data),
            'state' => $this->state,
        ];
    }
}

class DBCompareResultItem
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
    }
}