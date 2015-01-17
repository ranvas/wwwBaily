<?php
class wunderException extends CException {}
class wunderGroundRepository extends CComponent
{

    //доступ в wunderground
    private $account = 'bailynoreply@gmail.com';
    private $password = '){JHJIFZ1weather';
    private $key = '70985b6239051976';
    private $url = 'api.wunderground.com/api/';
    //разрешенные параметры
    private $callsPerDay = 500;
    private $callsPerCall = 17;//20 макс
    //получить все коды стран
    //http://www.wunderground.com/weather/api/d/docs?d=resources/country-to-iso-matching
    //получить все станции страны
    //http://api.wunderground.com/api/70985b6239051976/geolookup/q/EG.json

    private function callStation($name)
    {
        //формирование строки запроса
        $url = $this->url.$this->key.'/conditions'.$name.'.json';
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
                'Host: '.$this->url
            ),
        ));
        //выполнит, закрыть
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * функция, которая считает всякую хуйню и вызывает callStation,
     * функция запускается раз в час
     */
    public function update()
    {
        //проверка, что не превышен лимит в день
        $count = $this->getTodayCount();
        if ($count > ($this->callsPerDay - $this->callsPerCall))
        {
            throw new wunderException('дообновлялся');
        }
        //если все ок, то продолжаем обновлять
        $sql = 'select id from west order by lastUpdate ASC LIMIT '.$this->callsPerCall;
        $wests = Yii::app()->db->createCommand($sql)->queryColumn();
        foreach($wests as $west)
        {
            $data = $this->callStation($west);
            if(isset($data))
            {
                $count++;
                Yii::app()->db->createCommand()->update('west',array('count'=>$count,'raw'=>$data,'lastUpdate'=>time()),'id=\''.$west.'\'');
                sleep(10);
            }
            else
            {
                throw new wunderException('не надо расстраиваться, хуйня случается');
            }
        }
    }

    public function updateStation($westName)
    {
        //проверка, что не превышен лимит в день
        $count = $this->getTodayCount();
        if ($count > ($this->callsPerDay - $this->callsPerCall))
        {
            throw new wunderException('дообновлялся');
        }
        //если все ок, то продолжаем обновлять
        $data = $this->callStation($westName);
        if(isset($data))
        {
            $count++;
            Yii::app()->db->createCommand()->update('west',array('count'=>$count,'raw'=>$data,'lastUpdate'=>time()),'id=\''.$westName.'\'');
        }
        else
        {
            throw new wunderException('не надо расстраиваться, хуйня случается');
        }
    }

    public function getTodayCount()
    {
        $sql = 'select count,lastUpdate from west order by lastUpdate DESC LIMIT 1';
        $check = Yii::app()->db->createCommand($sql)->queryRow();
        if(date('z', $check['lastUpdate']) < date('z') && date('Y',$check['lastUpdate']) === date('Y'))
        {
            return 0;
        }
        else
        {
            return $check['count'];
        }

    }





}