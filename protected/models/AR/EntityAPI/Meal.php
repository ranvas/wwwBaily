<?php


/**
 * @property integer $entityId
 * @property string $mealCyr
 * @property string $params
 */
class Meal extends EntityAPI
{

    //region AR
    /**
     * @return string  таблица ассоциируемая с классом
     */
    public function tableName()
    {
        return 'meals';
    }

    /**
     * @return array правила валидации
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('entityId, mealCyr, params', 'required'),
            array('entityId', 'numerical', 'integerOnly'=>true),
            array('mealCyr', 'length', 'max'=>50),
            array('params', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('entityId, mealCyr', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array отношения между таблицами
     */
    public function relations()
    {
        return array(

        );
    }

    /**
     * @return array метки аттрибутов
     */
    public function attributeLabels()
    {
        return array(
            'entityId' => 'идентификатор',
            'mealCyr' => 'название',
        );
    }


    public function search()
    {
        $criteria=new CDbCriteria;
        $criteria->compare('entityId',$this->entityId);
        $criteria->compare('mealCyr',$this->mealCyr,true);
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
            'mealId'=>'entityId',
            'mealCyr'=>'mealCyr',
            'params'=>'params',
            'countries'=>function($data){
                foreach(explode(',', $data) as $item)
                {
                    //проверить, что страна существует
                    $country = Yii::app()->db->createCommand('select entityId from countries where entityId='.$item)->queryScalar();
                    if($country){
                        //записать в таблицу countryMeal строку
                        Yii::app()->db->createCommand()->insert('countryMeal',array('mealId'=>$this->entityId,'countryId'=>$country));
                    }

                }
            }
        );
    }

}