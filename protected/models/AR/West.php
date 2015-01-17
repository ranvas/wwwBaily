<?php

/**
 * Погодная станция
 * @property string $id
 * @property string $raw
 * @property string $name
 * @property string $lastUpdate
 * @property string $count
 */
class West extends CActiveRecord
{
    private $rawObject;

	/**
	 * @return string таблица соответствия в БД
	 */
	public function tableName()
	{
		return 'west';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{
		return array(
			array('id, name, count', 'required'),
            array('id','unique'),
			array('name, lastUpdate', 'length', 'max'=>25),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, raw, name, lastUpdate, count', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array отношения с другими AR моделями
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array описание аттрибутов
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Идентификатор',
			'raw' => 'Строка данных',
			'name' => 'Название',
			'lastUpdate' => 'Последнее обновление',
            'count'=> 'Счетчик за день',
            'countriesDataProvider'=>'Привязанные страны',
            'regionsDataProvider'=>'Привязанные курорты'
		);
	}



	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('raw',$this->raw,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('lastUpdate',$this->lastUpdate,true);
        $criteria->compare('count',$this->count,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Службная функция доступа к модели
     * @param string $className
     * @return CActiveRecord
     */
    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    public function getTemperature()
    {
        if(!(isset($this->rawObject)))
        {
            $this->rawObject = json_decode($this->raw);
        }
        return isset($this->rawObject->current_observation->temp_c) ? $this->rawObject->current_observation->temp_c." C" : "неверные данные";
    }
    public function getWeather()
    {
        if(!(isset($this->rawObject)))
        {
            $this->rawObject = json_decode($this->raw);
        }
        return isset($this->rawObject->current_observation->weather) ? $this->rawObject->current_observation->weather : "неверные данные";
    }






}
