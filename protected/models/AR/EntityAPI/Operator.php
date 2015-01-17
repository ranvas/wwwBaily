<?php

/**
 * @property string $entityId
 * @property string $name
 */
class Operator extends EntityAPI
{
	/**
	 * @return string Таблица ассоциируемая с сущностью
	 */
	public function tableName()
	{
		return 'operators';
	}

	/**
	 * @return array правила валидации полей модели
	 */
	public function rules()
	{
		return array(
			array('entityId, name', 'required'),
			array('name', 'length', 'max'=>63),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entityId, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array правила отношений с другими таблицами
	 */
	public function relations()
	{
		return array(
            'countries'=>array(self::MANY_MANY, 'Country', 'operatorCountry(operatorId, countryId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'countries'=>'Страны',
			'entityId' => 'Идентификатор оператора',
			'name' => 'Название',

		);
	}


	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('entityId',$this->entityId,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Operator the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    public function fieldMap()
    {
        return array(
            'operatorId'=>'entityId',
            'operatorName'=>'name',
            'params'=> function($data){
                foreach(explode(',',$data) as $country)
                {
                    $countryId = Yii::app()->db->createCommand('select entityId from countries where entityId='.$country)->queryScalar();
                    if($countryId)
                    {
                        Yii::app()->db->createCommand()->insert('operatorCountry',array('operatorId'=>$this->entityId, 'countryId'=>$countryId));
                    }
                }
            }
        );
    }
}
