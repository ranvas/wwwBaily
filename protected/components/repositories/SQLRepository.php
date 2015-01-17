<?php

class SQLException extends CException {}

/**
 * Class SQLRepository
 */
class SQLRepository extends CComponent
{
    private $modules = array(
        'membership'=>array(
            'tables'=>'Account,AuthItem,AuthItemChild,AuthAssignment',
            'create'=>'/srv/sql/membership/create_tables.sql',
            'delete'=>'/srv/sql/membership/delete_tables.sql',
            'update'=>'/srv/sql/membership/update_tables.sql'
        ),
        'api'=> array(
            'tables'=>'operatorCountry,operators,countryCategory,countries,currencies,meals,countryMeal,categories,regions,hotels,history,functions,functionInOrder,functionOutOrder',
            'create'=>'/srv/sql/api/create_tables.sql',
            'delete'=>'/srv/sql/api/delete_tables.sql',
            'update'=>'/srv/sql/api/update_tables.sql'
        ),
        'help'=> array(
            'tables'=>'west,records,westEntity,recordEntity',
            'create'=>'/srv/sql/help/create_tables.sql',
            'delete'=>'/srv/sql/help/delete_tables.sql',
            'update'=>'/srv/sql/help/update_tables.sql'
        ),
        'search'=>array(
            'tables'=>'cacheTours,cacheSearch,filterRegions,filterCountries,filterOperators,filters',
            'create'=>'/srv/sql/search/create_tables.sql',
            'delete'=>'/srv/sql/search/delete_tables.sql',
            'update'=>'/srv/sql/search/update_tables.sql'
        ),

    );
    private function createTables($moduleName)
    {
        if (array_key_exists($moduleName, $this->modules))
        {
            $connection = Yii::app()->db;
            $file = $this->modules[$moduleName]['create'];
            return functions::executeSQLFile($file, $connection);
        }
        else
        {
            throw new SQLException('Для создания таблиц нет модуля '.$moduleName);
        }
    }

    private function deleteTables($moduleName)
    {
        if (array_key_exists($moduleName, $this->modules))
        {
            $connection = Yii::app()->db;
            $file = $this->modules[$moduleName]['delete'];
            return functions::executeSQLFile($file, $connection);
        }
        else
        {
            throw new SQLException('Для удалении таблиц нет модуля '.$moduleName);
        }
    }
    private function updateTables($moduleName)
    {
        if (array_key_exists($moduleName, $this->modules))
        {
            $connection = Yii::app()->db;
            $file = $this->modules[$moduleName]['update'];
            return functions::executeSQLFile($file, $connection);
        }
        else
        {
            throw new SQLException('Для удалении таблиц нет модуля '.$moduleName);
        }
    }

    public function truncateTables($moduleName)
    {
        $test = 0;
        if (($this->deleteTables($moduleName))&&($this->createTables($moduleName))&&($this->updateTables($moduleName)))
        {
            return true;
        }
        else
        {
            throw new SQLException('Ошибка обновления модуля '.$moduleName);
        }
    }

    public function getTables($moduleName)
    {
        //таблицы на выходе
        $ret = array();
        if (array_key_exists($moduleName, $this->modules))
        {
            //таблицы одобренные верхновным ^
            $tables = explode(',',$this->modules[$moduleName]['tables']);
            foreach ($tables as $table)
            {
                if($this->tableExist($table))
                {
                    $ret[] = $table;
                }
            }
        }
        return $ret;
    }

    /**
     * Проверить таблицу на существование
     * @param $tableName
     * @return bool
     */
    public function tableExist($tableName)
    {
        $val = Yii::app()->db->schema->getTable($tableName);
        if($val)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function queryAll($select, $from, $where = null)
    {
        if ($where)
        {
            $sql = 'select '.$select.' from '.$from.' where '.$where;
        }
        else
        {
            $sql = 'select '.$select.' from '.$from;
        }

        try
        {
            $ret = Yii::app()->db->createCommand($sql)->queryAll();
            return $ret;
        }
        catch(CDbException $e)
        {
            throw new SQLException($e->getMessage());
        }

    }






}