<?php

class CacheTour extends Search
{
     /**
     * @return string таблица соответствия в БД
     */
    public function tableName()
    {
        return 'cacheTours';
    }

    /**
     * @return array правила валидации, не все
     */
    public function rules()
    {
        return array(

//            array('countryCode', 'length', 'min'=>1, 'max'=>15),
//            array('bDate, createDate, updateDate', 'length', 'min'=>6, 'max'=>15),
//            array('destQryStr, mealQryStr, catQryStr, operQryStr, hotelName, categoryDescription, roomType, mealDescription, destination, airCompany', 'length', 'max'=>63),
//            array('id, countryCode, bDate, duration, adQty, chQty, airCharge, userId', 'safe', 'on'=>'search'),
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

        );
    }

    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;




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

    public function getBDate2()
    {
        return $this->bDate1;
    }

    public function getDurationMax()
    {
        return $this->durationMin;
    }


}