<?php


/**
 * @property integer $currencyId
 * @property string $name
 */
class Currency extends EntityAPI
{
    //region AR
	/**
	 * @return string таблица ассоциируемая с классом
	 */
	public function tableName()
	{
		return 'currencies';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{
		return array(
			array('entityId, name', 'required'),
			array('entityId', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>63),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entityId, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array отношения между таблицами
	 */
	public function relations()
	{
		return array(
            'countries'=>array(self::HAS_MANY, 'Country', 'currencyId'),
		);
	}

	/**
	 * @return array метки аттрибутов
	 */
	public function attributeLabels()
	{
		return array(
			'entityId' => 'идентификатор',
			'name' => 'название',
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

		$criteria->compare('entityId',$this->entityId);
		$criteria->compare('name',$this->name,true);

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

    public function fieldMap()
    {
        return array(
            'currencyId'=>'entityId',
            'name'=>'name'
        );
    }

}
