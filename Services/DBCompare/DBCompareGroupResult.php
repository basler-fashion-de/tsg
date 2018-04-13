<?php

namespace BlaubandTSG\Services\DBCompare;

class DBCompareGroupResult
{
    private $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function addResult(DBCompareResult $result)
    {
        if(!empty($result)){
            $this->data[] = $result;
        }
    }

    public function __toArray()
    {
        $return = [];

        /** @var DBCompareResult $result */
        foreach ($this->data AS $result) {
            $return[] = $result->__toArray();
        }

        return $return;
    }
}