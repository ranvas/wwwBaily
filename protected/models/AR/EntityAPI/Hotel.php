<?php

/**
 * @property string $entityId идентификатор, задаваемый программно
 * @property integer $countryId идентификатор страны
 * @property integer $hotelId идентификатор отеля в БД Baily
 * @property string $name название отеля
 * @property integer $categoryId идентификатор категории
 * @property string $categoryDescription альтернативное описание категории
 * @property integer $regionId идентификатор курорта
 * @property array $names
 */
class Hotel extends EntityAPI
{

    //region AR
	/**
	 * @return string таблица ассоциируемая с классом
	 */
	public function tableName()
	{
		return 'hotels';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{
		return array(
			array('entityId, countryId, hotelId, name, categoryId, categoryDescription, regionId', 'required'),
			array('entityId, countryId, hotelId, categoryId, regionId', 'numerical', 'integerOnly'=>true),
			array('categoryDescription', 'length', 'max'=>63),
            array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entityId, countryId, hotelId, name, categoryId, categoryDescription, regionId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array отношения между таблицами
	 */
	public function relations()
	{
		return array(
            'region'=>array(self::BELONGS_TO, 'Region', 'regionId'),
            'country'=>array(self::BELONGS_TO, 'Country', 'countryId'),
            'category'=>array(self::BELONGS_TO, 'Category', 'categoryId'),

		);
	}

	/**
	 * @return array метки аттрибутов
	 */
	public function attributeLabels()
	{
		return array(
			'entityId' => 'Идентификатор',
			'countryId' => 'Идентификатор страны',
			'hotelId' => 'Идентификатор API',
			'name' => 'Название',
			'categoryId' => 'Идентифиатор категории',
			'categoryDescription' => 'Дополнительное описание категории',
			'regionId' => 'Идентификатор курорта',
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
		$criteria->compare('hotelId',$this->hotelId);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('categoryId',$this->categoryId);
		$criteria->compare('categoryDescription',$this->categoryDescription,true);
		$criteria->compare('regionId',$this->regionId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @param string $className
     * @return Hotel
     */
    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    //endregion

    /**
     * для корректной работы необходимо выполнение условия:
     * объявление полей hotelId и countryId должны идти ДО объявления name
     * небольшой костыль
     */
    public function fieldMap()
    {
        return array(
            'hotelId'=>'hotelId',
            'countryId'=>'countryId',
            'name'=>function($data){
                $this->entityId = functions::concatenateIds($this->countryId, $this->hotelId);
                $this->name = $data;
            },
            'categoryId'=>'categoryId',
            'categoryDescription'=>'categoryDescription',
            'regionId'=>function($data){
                $this->regionId = functions::concatenateIds($this->countryId,$data);
            }
        );
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return Yii::app()->db->createCommand()->select('hotelName')->from('operatorHotelName')->where('hotelId='.$this->entityId)->queryColumn();
    }

}
