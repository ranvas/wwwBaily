<?php

/**
 *
 * @property integer $entityId
 * @property integer $categoryText
 *
 */
class Category extends EntityAPI
{
    //region AR

	/**
	 * @return string таблица ассоциируемая с классом
	 */
	public function tableName()
	{
		return 'categories';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{
		return array(
			array('entityId', 'required'),
			array('entityId', 'numerical', 'integerOnly'=>true),
            array('categoryText', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('entityId, categoryText', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array отношения между таблицами
	 */
	public function relations()
	{
		return array(
			'countries' => array(self::MANY_MANY, 'Country',
                'countryCategory(categoryId, countryId)'),
		);
	}

	/**
	 * @return array метки аттрибутов
	 */
	public function attributeLabels()
	{
		return array(
			'entityId' => 'идентификатор',
			'categoryText' => 'Category Text',
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
		$criteria->compare('categoryText',$this->categoryText);

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
            'categoryId'=>'entityId',
            'categoryText'=>'categoryText',
            'countries'=>function($data){
                foreach(explode(',', $data) as $item){
                    //проверить, что страна существует
                    $country = Yii::app()->db->createCommand('select entityId from countries where entityId='.$item)->queryScalar();
                    if($country)
                    {
                        //записать в таблицу countryCategory строку
                        Yii::app()->db->createCommand()->insert('countryCategory',array('categoryId'=>$this->entityId,'countryId'=>$country));
                    }

                }
            }
        );
    }
}
