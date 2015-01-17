<?php

/**
 * @property string $id
 * @property string $text
 * @property string $authorId
 * @property string $redactorId
 * @property string $updateDate
 * @property string $createDate
 */
class Record extends CActiveRecord
{
	/**
	 * @return string таблица соответствия в БД
	 */
	public function tableName()
	{
		return 'records';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{
		return array(
			array('text, authorId, redactorId, updateDate, createDate, updateDate', 'required'),
			array('updateDate, createDate', 'length', 'max'=>25),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, text, authorId, redactorId, updateDate, createDate', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array связи таблицы
	 */
	public function relations()
	{
		return array(
            'author'=>array(self::BELONGS_TO, 'Account', 'authorId'),
            'redactor'=>array(self::BELONGS_TO, 'Account', 'redactorId'),
		);
	}

	/**
	 * @return array Описание аттрибутов
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Идентификатор',
			'text' => 'Текст',
			'authorId' => 'Идентификатор автора',
			'updateDate' => 'Время последнего обновления',
            'createDate' => 'Время создания',
            'redactorId'=> 'Идентификатор последнего изменившего'
		);
	}


	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('authorId',$this->authorId,true);
		$criteria->compare('updateDate',$this->updateDate,true);
        $criteria->compare('redactorId',$this->redactorId,true);
        $criteria->compare('createDate',$this->createDate,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Служебная функция доступа к модели
     * @param string $className
     * @return Record
     */
    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @return bool
     * действия до сохранения модели, но ПОСЛЕ валидации
     */
    public function beforeSave() {

        return parent::beforeSave();
    }
}
