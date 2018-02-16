<?php

namespace BlaubandOneClickSystem\Services\DBCompare;

use Doctrine\DBAL\Connection;

class DBCompareService
{
    public function compareTables($tableNames, Connection $hostConnection, Connection $guestConnection)
    {
        if (!is_array($tableNames)) {
            $tableNames = [$tableNames];
        }

        $return = new DBCompareGroupResult();

        foreach ($tableNames as $tableName) {
            $result = $this->compareTable($tableName, $hostConnection, $guestConnection);
            $return->addResult($result);
        }

        return $return;
    }

    public function compareTable($tableName, Connection $hostConnection, Connection $guestConnection)
    {
        $primaryKey = $hostConnection->fetchAll("SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'");
        $primaryKey = $primaryKey[0];

        if (!empty($primaryKey)) {
            $result = $this->compare($tableName, $hostConnection, $guestConnection, $primaryKey['Column_name']);
        } else {
            $result = $this->compare($tableName, $hostConnection, $guestConnection);
        }

        return $result;
    }

    private function compare($tableName, Connection $hostConnection, Connection $guestConnection, $primaryKey = null)
    {
        $result = new DBCompareResult($tableName);
        $hostData = $this->getTable($tableName, $hostConnection, $primaryKey);
        $guestData = $this->getTable($tableName, $guestConnection, $primaryKey);

        $keys = array_unique(array_merge(array_keys($hostData), array_keys($guestData)), SORT_REGULAR);

        foreach ($keys AS $key) {
            $result->addRow($hostData[$key], $guestData[$key]);
        }

        return $result;
    }

    private function getTable($tableName, Connection $connection, $primaryKey = null)
    {
        $sql = "SELECT * FROM $tableName";
        $data = $connection->fetchAll($sql);
        $return = [];

        if (empty($data)) {
            return [];
        }

        if (empty($primaryKey)) {
            foreach ($data as $d) {
                $return[md5(json_encode($d))] = $d;
            }
        } else {
            $return = array_combine(array_column($data, $primaryKey), $data);
        }

        ksort($return);
        return $return;
    }
}