<?php

/**
 * @property integer $id идентификатор
 * @property integer $countryKey идентификатор страны
 * @property string $destQryStr строка с идентификаторами выбранных курортов
 * @property string $bDate1 дата вылета минимальная
 * @property string $bDate2 дата вылета максимальная
 * @property integer $durationMin продолжительность ночей минимальная
 * @property integer $durationMax продолжительность ночей максимальная
 * @property integer $adQty количество взрослых
 * @property integer $chQty количество детей
 * @property integer $fstChd возраст первого ребенка
 * @property integer $sndChd возраст второго ребенка
 * @property string $mealQryStr строка с идентификаторами выбранного питания
 * @property string $catQryStr строка с идентификаторами выбранных категорий
 * @property integer $hotelQryStr строка с идентификаторами отелей внутри страны
 * @property string $operQryStr строка с идентификаторами операторов
 * @property boolean $airCharge учитывать топливный сбор или нет
 * @property integer $userId идентификатор пользователя
 * @property string $createDateTime время создания запроса
 * @property string $responseStr json-текст ответа от API
 * @property string $runTime время выполнения запроса
 */
class CacheSearch extends Search
{
    /**
     * @return string таблица соответствия в БД
     */
    public function tableName()
    {
        return 'cacheSearch';
    }


    public function rules() {
        $rules=parent::rules();
        return CMap::mergeArray($rules,array(

        ));
    }

    /**
     * @return array связи, не все
     */
    public function relations()
    {
        $relations=parent::relations();
        return CMap::mergeArray($relations,array(

        ));
    }

    /**
     * @return array описание аттрибутов, не все
     */
    public function attributeLabels()
    {
        $labels=parent::attributeLabels();
        return CMap::mergeArray($labels,array(

        ));
    }


    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=parent::search();



        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Служебная функция доступа к классу
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }





}