<?php
//добавить в updateFunctions обработку обновления функций, сейчас ничего не предпринимается
    //region exceptions and events

/**
 * Class APIException используется ошибки при работе с API
 */
class APIException extends CException {}

class APIEntityUpdateEvent extends CModelEvent
{
    public $params = array();
}
class APIEntityInstanceSaveEvent extends CModelEvent
{
    public $instance;
}

    //endregion

class APIRepository extends CComponent
{

    public $bugsEntity = array(67111155, 67111158, 402653187, 201326598, 402653190, 402653190, 402653217, 67111163,100666282, 100669559,234881584,436207768,452984833);


    //region служебные

    /**
     * В методе выполняется только вызов API-функции и возврат результата
     * @param $method
     * @param array $params
     * @return mixed
     */
    private function call($method, $params)
    {
        //формирование строки запроса
        $url = BAILY_API_URL.'/'.$method.($params ? '?'.implode('&', $params):'');
        //инициирование экземпляра curl и задание параметров
        $ch = curl_init();
        curl_setopt_array($ch,array(
            CURLOPT_HTTPGET => true,
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_RETURNTRANSFER => true,
            //количество секунд для ожидания установки соединения
            CURLOPT_CONNECTTIMEOUT => 5,
            //количество секунд для ожидания ответа
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTPHEADER => array(
                'charset=utf-8',
                'Host: www.baily.ru'
            ),
        ));
        //выполнит, закрыть
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * @param $func
     * @param $params
     * @param bool $new - true для подачи параметров без значений ключей
     * @return array
     * @throws APIException
     */
    private function getInSignature($func, $params, $new = false)
    {
        //получение сигнатуры входных параметров из БД
        $dbParams = Yii::app()->db->createCommand()->
            select('paramName')->
            from('functionInOrder')->
            order('order')->
            where('functionName=\''.$func.'\'')->
            queryColumn();
        $signature = array();
        //для передачи параметров без названия, сплошным потоком
        if($new)
        {
            //счетчик
            $i = 0;
            //входных параметров должно быть не меньше, чем API поддерживает
            foreach($dbParams as $dbParam)
            {
                $signature[] = $params[$i];
                $i++;
            }
        }
        else
        {
            foreach($dbParams as $dbParam)
            {
                if(isset($params[$dbParam]))
                {
                    $signature[] = $params[$dbParam];
                }
                else
                {
                    throw new APIException('Не хватает параметра '.$dbParam);
                }
            }
        }
        return $signature;

    }

    /**
     * @param $func
     * @return mixed
     * @throws APIException
     * Получить сигнатуру выходных параметров функции
     */
    private function getOutSignature($func)
    {
        $db = Yii::app()->db->createCommand();
        $signature['name'] = $db->
            select('containerName')->
            from('functions')->
            where('functionName=\''.$func.'\'')->
            queryScalar();
        $db->reset();
        $signature['out'] = $db->
            select('paramName')->
            from('functionOutOrder')->
            order('order')->
            where('functionName=\''.$func.'\'')->
            queryColumn();
        if ((count($signature['out']) > 0) && ($signature['name']))
        {
            return $signature;
        }
        else
        {
            throw new APIException(sprintf('ошибка при получении outSignature для функции %s',$func));
        }


    }

    /**
     * @param $entityClass
     * @param $func
     * @param $params
     * @return int
     * @throws APIException
     * Обновить сущность
     */
    private function updateEntity($entityClass, $func, $params)
    {
        //получить данные
        $inSignature = $this->getInSignature($func, $params);
        if(count($inSignature) > 0)
        {
            if ($inSignature[0]==='')
            {
                throw new APIException('Найденный баг в API.dll.Если передать в url первый параметр пустой, то вернется ошибка. Например /getHotels?');
            }

        }
        $data = json_decode($this->call($func, $inSignature), true);
        $outSignature = $this->getOutSignature($func);
        //счетчик сущностей
        $i = 0;
        //проверить, что вернулся нужный контейнер
        if(!(isset($data[$outSignature['name']])))
        {
            throw new APIException(sprintf('при вызове %s от API не вернулся нужный контейнер с данными',$func));
        }
        //пройти по каждому полученному элементу данных
        foreach ($data[$outSignature['name']] as $item)
        {
            //создать экземпляр переданного класса
            $instance = new $entityClass;
            //счетчик значений данных в пределах одного экземпляра
            $j = 0;
            $map = $instance->fieldMap();

            //присвоение элементам экземпляра значений по таблице сигнатуры
            foreach($outSignature['out'] as $parameter)
            {
                //если перебираемое значение присутствует у инициализируемой сущности
                if(array_key_exists($parameter, $map))
                {
                    //если значение надо обработать
                    if(is_callable($map[$parameter]))
                    {
                        //передать обработчику соответствующего параметра текущее значение информации
                        $map[$parameter]($item[$j]);
                    }
                    else
                    {
                        //записать в соответствующее поле текущее значение информации
                        $instance->$map[$parameter] =  $item[$j];
                    }
                }
                $j++;
            }
            //установить время
            $instance->setDate();
            //если id сущности уже присутствует в таблице, то новую создавать не надо

            if(Yii::app()->db->createCommand('select entityId from '.$instance->tableName().' where entityId='.$instance->entityId)->queryScalar())
            {
                $instance->isNewRecord = false;
            }
            //вызов события перед сохранением
            if($this->hasEventHandler('onBeforeInstanceSave'))
            {
                $event = new APIEntityInstanceSaveEvent(__CLASS__);
                $event->instance = $instance;
                $this->onBeforeInstanceSave($event);
            }
            //если получилось сохранить сущность, то инкремент счетчика, если нет, то вызвать исключение
            if($instance->save())
            {
                $i++;
                //вызов события после сохранения
                if($this->hasEventHandler('onAfterInstanceSave'))
                {
                    $event = new APIEntityInstanceSaveEvent(__CLASS__);
                    $event->instance = $instance;
                    $this->onAfterInstanceSave($event);
                }
            }
            else
            {
                //известные баги
                if (in_array($instance->entityId, $this->bugsEntity))
                {

                }
                else
                {
                    CVarDumper::dump($instance);
//                  //неизвестные баги
                    throw new APIException(sprintf('сохранить %s не получилось, id=%d',$entityClass, $instance->entityId));
                }
            }
        }
        //ведение истории запросов
        Yii::app()->db->createCommand()->insert('history',array('funcName'=>$func, 'lastUpdate'=>date("D M d, Y G:i"), 'count'=>$i));
        return $data;
    }

    private function rawExist($table, $condition)
    {
        $count = Yii::app()->db->createCommand()->select('count(*)')->from($table)->where($condition)->queryScalar();
        return $count > 0;
    }

    /**
     * @param $func
     * @return int
     * @throws APIException
     * обновить сигнатуры функции
     */
    private function updateFunction($func)
    {
        //количество добавленных записей
        $i=0;
        //получить сигнатуру функции
        $functionData = $this->call("getFunctions",array($func));
        //парсинг полученных данных
        $functionData = new SimpleXMLElement($functionData);
        //альтернативный конструктор
        //$functionData = simplexml_load_string($functionData);
        //пройти по детям и проверить, что вернулся запрашиваемый элемент
        foreach($functionData->children() as $child)
        {
            if($child->getName() === $func)
            {
                //проверить передаваемый тип
                if(trim($child['type'])==='application/json')
                {
                    //содержимое where для запросов
                    $fnCondition = sprintf('functionName = \'%s\'',$func);
                    if($this->rawExist('functions', $fnCondition))
                    {
                        //удалить все старые данные
                        Yii::app()->db->createCommand()->delete('functions', $fnCondition);
                        Yii::app()->db->createCommand()->delete('functionInOrder', $fnCondition);
                        Yii::app()->db->createCommand()->delete('functionOutOrder', $fnCondition);
                    }
                    //внести данные о имени контейнера
                    Yii::app()->db->createCommand()->insert('functions',array(
                        'functionName'=>$func,
                        'containerName'=>trim($child['name']),
                        'description'=>$child->description
                    ));
                    $i++;
                    foreach($child->inparam as $inparam)
                    {
                        //внести данные о входных параметрах
                        Yii::app()->db->createCommand()->insert('functionInOrder',array(
                            'functionName'=>$func,
                            'paramName'=>trim($inparam),
                            'order'=>trim($inparam['order']),
                            'paramType'=>trim($inparam['type']),
                        ));
                        $i++;
                    }
                    foreach ($child->outparam as $outparam)
                    {
                        //внести данные о выходных параметрах
                        Yii::app()->db->createCommand()->insert('functionOutOrder',array(
                            'functionName'=>$func,
                            'paramName'=>trim($outparam),
                            'order'=>trim($outparam['order']),
                            'paramType'=>trim($outparam['type']),
                        ));
                        $i++;
                    }
                }
                //если ребенка найти, то по другим детям бегать не надо
                break;
            }
            else
            {
                throw new APIException(sprintf('Не найден запрашиваемый элемент в getFunction при обработке запроса %s',$func));
            }
        }
        return $i;
    }




    //endregion

    //region события


    public function onBeforeInstanceSave(APIEntityInstanceSaveEvent $event)
    {
        $this->raiseEvent('onBeforeInstanceSave', $event);
    }
    public function onAfterInstanceSave(APIEntityInstanceSaveEvent $event)
    {
        $this->raiseEvent('onAfterInstanceSave', $event);
    }
    public function onBeforeUpdateCountries(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeUpdateCountries', $event);
    }
    public function onAfterUpdateCountries(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterUpdateCountries', $event);
    }
    public function onBeforeUpdateCurrencies(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeUpdateCurrencies', $event);
    }
    public function onAfterUpdateCurrencies(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterUpdateCurrencies', $event);
    }
    public function onBeforeUpdateRegions(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeUpdateRegions', $event);
    }
    public function onAfterUpdateRegions(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterUpdateRegions', $event);
    }
    public function onBeforeUpdateHotels(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeUpdateHotels', $event);
    }
    public function onAfterUpdateHotels(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterUpdateHotels', $event);
    }
    public function onBeforeUpdateCategories(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeUpdateCategories', $event);
    }
    public function onAfterUpdateCategories(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterUpdateCategories', $event);
    }
    public function onBeforeUpdateMeals(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeUpdateMeals', $event);
    }
    public function onAfterUpdateMeals(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterUpdateMeals', $event);
    }
    public function onBeforeUpdateOperators(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeUpdateOperators', $event);
    }
    public function onAfterUpdateOperators(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterUpdateOperators', $event);
    }
    public function onBeforeUpdateFunctions(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeUpdateFunctions', $event);
    }
    public function onAfterUpdateFunctions(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterUpdateFunctions', $event);
    }
    public function onBeforeGetResult(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onBeforeGetResult', $event);
    }
    public function onAfterGetResult(APIEntityUpdateEvent $event)
    {
        $this->raiseEvent('onAfterGetResult', $event);
    }


    //endregion

    //region обновление API

    /**
     * обновление списка стран
     */
    public function updateCountries()
    {
        //объявление события перед обновлением
        if ($this->hasEventHandler('onBeforeUpdateCountries'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onBeforeUpdateCountries($event);
        }
        //входные параметры
        $params = array();
        $count = $this->updateEntity('Country', 'getCountries', $params);
        //объявление события после обновления
        if ($this->hasEventHandler('onAfterUpdateCountries'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onAfterUpdateCountries($event);
        }

        return $count;
    }

    /**
     * обновление списка валют
     */
    public function updateCurrencies()
    {
        if ($this->hasEventHandler('onBeforeUpdateCurrencies'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onBeforeUpdateCurrencies($event);
        }
        //входные параметры
        $params = array();
        $count = $this->updateEntity('Currency', 'getCurrencies', $params);
        if ($this->hasEventHandler('onAfterUpdateCurrencies'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onAfterUpdateCurrencies($event);
        }
        return $count;
    }

    /**
     * обновление списка курортов
     */
    public function updateRegions()
    {
        if ($this->hasEventHandler('onBeforeUpdateRegions'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onBeforeUpdateRegions($event);
        }
        //входные параметры
        $params = array();
        $count = $this->updateEntity('Region', 'getRegions', $params);
        if ($this->hasEventHandler('onAfterUpdateRegions'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onAfterUpdateRegions($event);
        }
        return $count;
    }

    /**
     * обновление списка отелей
     */
    public function updateHotels()
    {
        if ($this->hasEventHandler('onBeforeUpdateHotels'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onBeforeUpdateHotels($event);
        }
        //входные параметры
        $params = array('countryId'=>0);
        $count = $this->updateEntity('Hotel', 'getHotels', $params);
        if ($this->hasEventHandler('onAfterUpdateHotels'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onAfterUpdateHotels($event);
        }
        return $count;
    }

    public function updateHotelsByCountryId($countryId)
    {
        //входные параметры
        $params = array('countryId'=>$countryId);
        if ($this->hasEventHandler('onBeforeUpdateHotels'))
        {
            $event = new APIEntityUpdateEvent();
            $event->params = $params;
            $this->onBeforeUpdateHotels($event);
        }

        $count = $this->updateEntity('Hotel', 'getHotels', $params);

        if ($this->hasEventHandler('onAfterUpdateHotels'))
        {
            $event = new APIEntityUpdateEvent();
            $event->params = $params;
            $this->onAfterUpdateHotels($event);
        }
        return $count;
    }

    /**
     * Обновление списка категорий
     */
    public function updateCategories()
    {
        if ($this->hasEventHandler('onBeforeUpdateCategories'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onBeforeUpdateCategories($event);
        }
        $params = array();
        //очистить таблицу countryCategory
        Yii::app()->db->createCommand()->truncateTable('countryCategory');
        $count = $this->updateEntity('Category','getCategories', $params);

        if ($this->hasEventHandler('onAfterUpdateCategories'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onAfterUpdateCategories($event);
        }
        return $count;
    }

    /**
     * @throws APIException
     * Установка displayActive для курортов, которые подпадают под влияние других курортов
     *
     */
    public function refactoringHotelsAndRegions()
    {
        //получить список курортов у которых есть params
        $sql = 'select entityId, countryId, params from regions where params is not null';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rows as $raw)
        {
            //получить отдельный угнетенный курорт из params
            foreach (explode(',',$raw['params']) as $param)
            {
                //сформировать id угнетенного курорта
                $regionId = functions::concatenateIds($raw['countryId'],$param);
                //получить id основного курорта
                $entityId = $raw['entityId'];
                //если id угнетенного курорта совпадает с id выбранного курорта, то это главный курорт
                if($regionId == $entityId)
                {

                    //установить displayActive в true
                    Yii::app()->db->createCommand()->update('regions',array('displayActive'=>'1'),'entityId=:regionId',array(':regionId'=>$regionId));
                }
                else
                {
                    //установить displayActive в false
                    Yii::app()->db->createCommand()->update('regions',array('displayActive'=>'0'),'entityId=:regionId',array(':regionId'=>$regionId));
                    //все отели, которые находятся в угнетенном курорте переезжают в господствующий курорт
                    Yii::app()->db->createCommand()->update('hotels',array('regionId'=>$entityId),'regionId=:regionId',array(':regionId'=>$regionId));
                }
            }
        }
        //все остальные отели не отображаются
        Yii::app()->db->createCommand()->update('regions',array('displayActive'=>'0'),'displayActive IS NULL');
    }


    /**
     * Обновить таблицы Meal и countryMeal
     * @return int
     */
    public function updateMeals()
    {
        if ($this->hasEventHandler('onBeforeUpdateMeals'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onBeforeUpdateMeals($event);
        }
        $params = array();
        //очистить таблицу countryMeal
        Yii::app()->db->createCommand()->truncateTable('countryMeal');
        //обновить список питаний
        $count = $this->updateEntity('Meal', 'getMeals', $params);
        if ($this->hasEventHandler('onAfterUpdateMeals'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onAfterUpdateMeals($event);
        }
        return $count;
    }

    /**
     * Обновить таблицы operators и operatorCountry
     * @return int
     */
    public function updateOperators()
    {
        if ($this->hasEventHandler('onBeforeUpdateOperators'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onBeforeUpdateMeals($event);
        }
        $params = array();
        //очистить таблицу привязок операторов к странам
        Yii::app()->db->createCommand()->truncateTable('operatorCountry');
        $count = $this->updateEntity('Operator','getOperators', $params);
        if ($this->hasEventHandler('onAfterUpdateOperators'))
        {
            $event = new APIEntityUpdateEvent();
            $this->onAfterUpdateOperators($event);
        }
        return $count;
    }

    /**
     * @param string $func название функции на обновление
     * @return int количество отработавших функций
     */
    public function updateFunctions($func = 'all')
    {
        if ($this->hasEventHandler('onBeforeUpdateFunctions'))
        {
            $event = new APIEntityUpdateEvent();
            $event->params = $func;
            $this->onBeforeUpdateFunctions($event);
        }
        //количество отработавших функций
        $count = 0;
        //обновить все функции
        if ($func ==='all')
        {
            if($this->updateFunction('getCurrencies') > 0)
            {
                $count++;
            }
            if($this->updateFunction('getCountries') > 0)
            {
                $count++;
            }
            if($this->updateFunction('getCategories') > 0)
            {
                $count++;
            }
            if($this->updateFunction('getMeals') > 0)
            {
                $count++;
            }
            if($this->updateFunction('getRegions') > 0)
            {
                $count++;
            }
            if($this->updateFunction('getHotels') > 0)
            {
                $count++;
            }
            if($this->updateFunction('getOperators') > 0)
            {
                $count++;
            }
            if($this->updateFunction('getResults') > 0)
            {
                $count++;
            }
        }
        //обновить конкретную функцию
        else
        {
            if($this->updateFunction($func) > 0)
            {
                $count++;
            }

        }
        if ($this->hasEventHandler('onAfterUpdateFunctions'))
        {
            $event = new APIEntityUpdateEvent();
            $event->params = $func;
            $this->onAfterUpdateFunctions($event);
        }
        return $count;
    }

    public function getFunctionsNames()
    {
        $sql = 'select functionName from functions';
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }

    public function getEagerFunctions()
    {
        $sql = 'select f.functionName as function,
        f.containerName as name,
        f.description as description,
        GROUP_CONCAT(DISTINCT _in.paramName ORDER by _in.order ASC SEPARATOR \', \') as inParams,
        GROUP_CONCAT(DISTINCT outT.paramName ORDER by outT.order ASC SEPARATOR \', \') as outParams
        from functions f
        left join `functionInOrder` _in ON f.functionName  = _in.functionName
        left join `functionOutOrder` outT ON f.functionName  = outT.functionName
        GROUP BY f.functionName;';
        return Yii::app()->db->createCommand($sql)->queryAll();
    }


    //endregion

    //region поисковые запросы

    public function getResultSignature()
    {

        return $this->getOutSignature('getResults');
    }


    /**
     * @param $params
     * @param bool $new true - для подачи параметров без значений ключей
     * @return mixed
     */
    public function getResults($params, $new = false)
    {
        $time = time();
        if ($this->hasEventHandler('onBeforeGetResult'))
        {
            $event = new APIEntityUpdateEvent();
            $event->params = $params;
            $event->params['startTime'] = $time;
            $this->onBeforeGetResult($event);
        }
        $signature = $this->getInSignature('getResults', $params, $new);
        $result = $this->call('getResults', $signature);
        if ($this->hasEventHandler('onAfterGetResult'))
        {
            $event = new APIEntityUpdateEvent();
            $event->params = $result;
            $event->params['startTime'] = $time;
            $this->onAfterGetResult($event);
        }
        return $result;

    }

    //endregion





}