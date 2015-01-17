<?php

/**
 * Если фильтр не привязан ни к одной стране, то используются все страны. Та же логика с курортами и операторами.
 * @property string $id
 * @property string $name
 * @property string $managerId
 * @property integer $vision; 1 - видит только назначенный менеджер; 2 - все у кого есть boFilter; 3 - все, все, все
 * @property string $description
 */

class Filter extends CActiveRecord
{


	/**
	 * @return string таблица ассоциированная с моделью
	 */
	public function tableName()
	{
		return 'filters';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, managerId, vision, description', 'required', 'on'=>'create'),
            array('id', 'required', 'on'=>'update'),
            array('description', 'safe', 'on'=>'update'),
			array('vision', 'numerical', 'integerOnly'=>true,'min'=>1,'max'=>'3'),
			array('name', 'length', 'min'=>4,'max'=>63),
            array('name', 'unique'),
			array('managerId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, managerId, vision, description', 'safe', 'on'=>'search'),
//            array('managerr', 'safe'),
		);
	}

	/**
	 * @return array связи таблицы
	 */
	public function relations()
	{
		return array(
            'manager'=>array(self::BELONGS_TO, 'Account', 'managerId'),
            'countries'=>array(self::MANY_MANY, 'Country', 'filterCountries(filterId, entityId)'),
            'regions'=>array(self::MANY_MANY, 'Region', 'filterRegions(filterId, entityId)'),
            'operators'=>array(self::MANY_MANY, 'Operator', 'filterOperators(filterId, entityId)')
		);
	}

	/**
	 * @return array описания полей
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Идентификатор',
			'name' => 'Название',
			'managerId' => 'Идентификатор ответственого за фильтр',
			'vision' => 'Видимость',
			'description' => 'Описание',
            'manager.username'=>'Менеджер',
		);
	}


	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('managerId',$this->managerId,true);
        $criteria->compare('description',$this->description,true);
        $criteria->addCondition('vision = 3');
        //можно видеть vision 1, тем у кого совпадает managerId
        $criteria->addCondition('(vision = 1) and (managerId = "'.Yii::app()->user->id.'")','OR');
        //если filterAdmin, то можно видеть vision 2
        if(Yii::app()->user->checkAccess("filterAdmin"))
        {
            $criteria->addCondition('vision = 2','OR');
        }
        if(Yii::app()->user->checkAccess("root"))
        {
            $criteria->addCondition('vision = 1','OR');
        }
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
     * Служебная функция доступа к модели
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}







}
