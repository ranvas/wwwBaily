<?php

class functions
{
    /**
     * создать рандомную строку указанной длинны
     * @param $length
     * @return string
     */
    public static function generateString($length)
    {
        $random= "";
        srand((double)microtime()*1000000);
        $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char_list .= "abcdefghijklmnopqrstuvwxyz";
        $char_list .= "1234567890";
        // Add the special characters to $char_list if needed

        for($i = 0; $i < $length; $i++)
        {
            $random .= substr($char_list,(rand()%(strlen($char_list))), 1);
        }
        return $random;
    }

    /**
     * выполнить sql-инструкции
     * @param $file полный путь к sql файлу
     * @param $connection соединение с БД
     * @return bool
     */
    public static function executeSQLFile($file, $connection)
    {
        if((!$connection)||(!file_exists($file))){
            return false;
        }
        $sqlStream = file_get_contents($file);
        $sqlStream = rtrim($sqlStream);
        $newStream = preg_replace_callback("/\((.*)\)/", create_function('$matches', 'return str_replace(";"," $$$ ",$matches[0]);'), $sqlStream);
        $sqlArray = explode(";", $newStream);
        foreach ($sqlArray as $value)
        {
            if (!empty($value))
            {
                $sql = str_replace(" $$$ ", ";", $value) . ";";
                $connection->pdoInstance->exec($sql);
            }
        }
        return true;
    }

    /**
     * парсит xml-файл или xml-текст в массив
     * @param $element
     * @param array $arr
     * @return array
     */
    public static function xml2array($element, $arr = array())
    {
        if(is_string($element))
        {
            $element = (strlen($element) > 5 && substr($element, -4) === '.xml')
                ? simplexml_load_file(DATAPATH.$element)
                : simplexml_load_string($element);
        }
        $iter = 0;
        foreach($element->children() as $b)
        {
            $a = $b->getName();
            if(!$b->children()){
                $arr[$a] = trim($b[0]);
            }
            else{
                $arr[$a][$iter] = array();
                $arr[$a][$iter] = self::xml2array($b,$arr[$a][$iter]);
            }
            $iter++;
        }
        return $arr;
    }

    /**
     * побитово объединяет tinyint id и mediumint, на выходе int
     * @param $first
     * @param $second
     * @return int
     * @throws CException
     */
    public static function concatenateIds($first, $second)
    {
        if(($first > 255) || ($first < 0) || ($second > 16777215) || ($second < 0))
        {
            throw new CException('Неверный размер данных при попытке генерации entityId. First: '.$first.' second: '.$second, 'error');
        }
        else
        {
            $primary = $first << 24;
            $primary += $second;
            return $primary;
        }
    }

    /**
     * выделяет из объединенного id, mediumint - второй id
     * @param $primary
     * @return int
     */
    public static function separationIdsMedium($primary)
    {
        return ($primary >> 24) % 255;

    }

    /**
     * выделяет из объединенного id tinyint - countryId
     * @param $primary
     * @return int
     */
    public static function separationIdsTiny($primary)
    {
        return ($primary >> 16) % 65535;
    }


    /**
     * формирует список для dropDownList из результатов sql-выборки
     * @param $inArray
     * @param null $key
     * @param null $value
     * @return array
     */
    public static function compactList($inArray, $key = null, $value = null)
    {
        $outArray = array();
        if((isset($key)&&(isset($value))))
        {
            foreach ($inArray as $item)
            {
                $outArray[$item[$key]] = $item[$value];
            }
        }
        else
        {
            foreach ($inArray as $item)
            {
                $outArray[$item] = $item;
            }
        }
        return $outArray;

    }

}