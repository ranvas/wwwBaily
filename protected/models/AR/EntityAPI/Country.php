<?php

/**
 * @property integer $entityId
 * @property string $name
 * @property string $nameCyr
 * @property string $countryCode
 * @property integer $currencyId
 */
class Country extends EntityAPI
{

    //region AR
	/**
	 * @return string таблица ассоциируемая с классом
	 */
	public function tableName()
	{
		return 'countries';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{

		return array(
			array('entityId, name, currencyId', 'required'),
			array('entityId, currencyId', 'numerical', 'integerOnly'=>true),
			array('name, nameCyr', 'length', 'max'=>63),
			array('countryCode', 'length', 'max'=>15),
			array('countryId, name, nameCyr, countryCode, currencyId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array отношения между таблицами
	 */
	public function relations()
	{
		return array(
            'currency'=>array(self::BELONGS_TO, 'Currency', 'currencyId'),
            'hotels'=>array(self::HAS_MANY, 'Hotel', 'countryId'),
            'regions'=>array(self::HAS_MANY, 'Region', 'countryId'),
            'operators'=>array(self::MANY_MANY, 'Operator', 'operatorCountry(countryId, operatorId)'),
		);
	}

	/**
	 * @return array метки аттрибутов
	 */
	public function attributeLabels()
	{
		return array(
			'countryId' => 'Идентификатор',
			'name' => 'Название',
			'nameCyr' => 'Название',
			'countryCode' => 'Код страны',
			'currencyId' => 'Валюта',
            'regions'=>'Курорты',
            'hotels'=>'Отели'
		);
	}



	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('entityId',$this->entityId);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('nameCyr',$this->nameCyr,true);
		$criteria->compare('countryCode',$this->countryCode,true);
		$criteria->compare('currencyId',$this->currencyId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @param string $className
     * @return Country
     */
    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    //endregion

    public function fieldMap()
    {
        return array(
            'countryId'=>'entityId',
            'name'=>'name',
            'nameCyr'=>'nameCyr',
            'countryCode'=>'countryCode',
            'currencyId'=> function($data){
                $this->currencyId = $data;
            }
        );
    }


}
