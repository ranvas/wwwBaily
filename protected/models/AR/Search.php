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
 * @property integer $ChQty количество детей
 * @property integer $fstChd возраст первого ребенка
 * @property integer $sndChd возраст второго ребенка
 * @property string $mealQryStr строка с идентификаторами выбранного питания
 * @property string $catQryStr строка с идентификаторами выбранных категорий
 * @property integer $priceMin минимальная цена
 * @property integer $priceMax максимальная цена
 * @property integer $hotelQryStr строка с идентификаторами отелей внутри страны
 * @property string $operQryStr строка с идентификаторами операторов
 * @property boolean $airCharge учитывать топливный сбор или нет
 * @property integer $userId идентификатор пользователя
 * @property string $createDateTime время создания запроса
 * @property string $responseStr json-текст ответа от API
 *
 */
abstract class Search extends CActiveRecord
{

    /**
     * @return array правила валидации, не все
     */
    public function rules()
    {
        return array(
            array('countryKey, bDate1, bDate2, durationMin, durationMax, adQty, chQty, fstChd, sndChd, airCharge, userId', 'required'),
            array('durationMin, durationMax', 'numerical', 'integerOnly'=>true),
            array('durationMin', 'compare', 'compareAttribute'=>'durationMax', 'operator'=> '<='),

        );
    }

    /**
     * @return array связи, не все
     */
    public function relations()
    {
        return array(

        );
    }

    /**
     * @return array описание аттрибутов, не все
     */
    public function attributeLabels()
    {
        return array(
            'id'            => 'Идентификатор',
            'countryKey'   => 'Идентификатор страны',
            'bDate1'        => 'дата вылета минимальная',
            'bDate2'        => 'дата вылета максимальная',
            'durationMin'   => 'продолжительность ночей минимальная',
            'durationMax'   => 'продолжительность ночей максимальная',
            'adQty'         => 'количество взрослых',
            'ChQty'         => 'количество детей',
            'fstChd'        => 'возраст первого ребенка',
            'sndChd'        => 'возраст второго ребенка',
            'mealQryStr'    => 'строка с идентификаторами выбранного питания',
            'catQryStr'     => 'строка с идентификаторами выбранных категорий',
            'priceMin'      => 'минимальная цена',
            'priceMax'      => 'минимальная цена',
            'hotelQryStr'   => 'строка с идентификаторами отелей внутри страны',
            'operQryStr'    => 'строка с идентификаторами операторов',
            'airCharge'     => 'учитывать топливный сбор или нет',
            'userId'        => 'идентификатор пользователя',
            'createDateTime'=> 'время создания запроса',
            'responseStr'   => 'json-текст ответа от API',
        );
    }

    public function search()
    {
        $criteria=new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('countryKey', $this->countryKey);
        $criteria->compare('bDate1', $this->bDate1);
        $criteria->compare('bDate2', $this->bDate2);
        $criteria->compare('durationMin', $this->durationMin);
        $criteria->compare('durationMax', $this->durationMax);
        $criteria->compare('adQty', $this->adQty);
        $criteria->compare('ChQty', $this->adQty);
        $criteria->compare('priceMin', $this->priceMin);
        $criteria->compare('priceMax', $this->priceMax);
        $criteria->compare('airCharge', $this->airCharge);
        $criteria->compare('userId', $this->userId);
        $criteria->compare('createDateTime', $this->createDateTime);
        return $criteria;

    }

    /**
     * Служебная функция доступа к классу
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

}
