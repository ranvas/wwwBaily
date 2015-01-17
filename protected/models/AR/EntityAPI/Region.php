<?php

/**
 * @property string $entityId
 * @property integer $countryId
 * @property integer $regionId
 * @property string $name
 * @property string $nameCyr
 * @property integer $displayActive - указывает, отображать ли курорт на форме или нет
 * @property string $params - айдишники курортов, над которыми данный курорт главенствует.
 * @property string $date
 *
 */
class Region extends EntityAPI
{

    //region AR
    /**
     * @return string таблица ассоциируемая с классом
     */
	public function tableName()
	{
		return 'regions';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{
		return array(
			array('entityId, countryId, regionId, name, nameCyr', 'required'),
			array('countryId, regionId, entityId', 'numerical', 'integerOnly'=>true),
			array('name, nameCyr', 'length', 'max'=>63),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entityId, countryId, regionId, name, nameCyr', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'hotels'=>array(self::HAS_MANY, 'Hotel', 'regionId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'entityId' => 'Идентификатор',
			'countryId' => 'Идентификатор страны',
			'regionId' => 'Идентификатор',
			'name' => 'Название',
			'nameCyr' => 'Название',
            'hotels'=> 'Отели'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('entityId',$this->entityId,true);
		$criteria->compare('countryId',$this->countryId);
		$criteria->compare('regionId',$this->regionId);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('nameCyr',$this->nameCyr,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Статичный метод доступа к объекту
     */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    //endregion

    /**
     * для корректной работы необходимо выполнение условия:
     * объявление полей regionId и countryId должны идти ДО объявления name
     * небольшой костыль
     */
    public function fieldMap()
    {
        return array(
            'regionId'=>'regionId',
            'countryId'=>'countryId',
            'name'=>function($data){
                if (isset($this->countryId) && isset($this->regionId)){
                    $this->entityId = functions::concatenateIds($this->countryId, $this->regionId);
                    $this->name = $data;
                }
                else{
                    throw new APIException('неправильная подача параметров при инициализации '.$data);
                }
            },
            'nameCyr'=>'nameCyr',
            'params'=>'params'
        );
    }
}
