<?php

namespace BlaubandOneClickSystem\Services\DBCompare;

use Doctrine\DBAL\Connection;

class DBCompareService
{
    public function compareTable($tableName, Connection $hostConnection, Connection $guestConnection)
    {
        $primaryKey = $hostConnection->fetchAll("SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'");
        $primaryKey = $primaryKey[0];

        if (!empty($primaryKey)) {
            $result = $this->compareByPrimaryKey($tableName, $primaryKey['Column_name'], $hostConnection, $guestConnection);
        }else{
            //Compare without primary key
        }

        return $result;
    }

    public function compareTables($tableNames, Connection $hostConnection, Connection $guestConnection)
    {
        if(!is_array($tableNames)){
            $tableNames = [$tableNames];
        }

        $return = new DBCompareGroupResult();

        foreach ($tableNames as $tableName){
            $result = $this->compareTable($tableName, $hostConnection,  $guestConnection);
            $return->addResult($result);
        }

        return $return;
    }

    private function compareByPrimaryKey($tableName, $primaryKey, Connection $hostConnection, Connection $guestConnection)
    {
        $result = new DBCompareResult($tableName);
        $hostData = $this->getTableByPrimaryKey($tableName, $primaryKey, $hostConnection);
        $guestData = $this->getTableByPrimaryKey($tableName, $primaryKey, $guestConnection);

        $keys = array_unique(array_merge(array_keys($hostData),array_keys($guestData)), SORT_REGULAR);

        foreach ($keys AS $key){
            $result->addRow($hostData[$key], $guestData[$key]);
        }

        return $result;
    }

    private function getTableByPrimaryKey($tableName, $primaryKey, Connection $connection)
    {
        $sql = "SELECT * FROM $tableName ORDER BY $primaryKey";
        $data = $connection->fetchAll($sql);

        if(!empty($data)){
            return array_combine(array_column($data, $primaryKey), $data);
        }else{
            return [];
        }
    }
}