<?php


class Role extends AuthItem
{

    private $_children;

    public function rules()
    {
        return array(
            array('name','unique','on'=>'create,update'),
            array('name, children','required','on'=>'create,update'),
            array('name','length','min'=>'4','max'=>'150','on'=>'create,update'),
            array('description,data','safe', 'on'=>'create,update'),
        );

    }

    public function relations()
    {
        return array(
//            'children'=>array(self::MANY_MANY, 'AuthItem', 'AuthItemChild(parent,child)')
        );
    }

    public function setChildren($value)
    {
        $this->children = $value;
    }

    /**
     * @return array
     * Получить привязки зависимых правил к этой роли
     */
    public function getChildren()
    {
        if (!(isset($this->_children)))
        {
            $this->_children = Yii::app()->dm->membership->getAllChildNameByParentName($this->name);
        }
        return $this->_children;
    }




    public function search()
    {
        $criteria=new CDbCriteria;
        $criteria->compare('name',$this->name);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('bizrule',$this->bizrule,true);
        $criteria->compare('data',$this->data);
        $criteria->addInCondition('type',array('2'));
        $criteria->addNotInCondition('name',array('root'));
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,

        ));
    }
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return CMap::mergeArray($labels,array(
            'children'=>'Правила',
            'name'=>'Имя роли',
            'description'=>'Описание роли',
            'data'=>'Неведома зверюшка'

        ));
    }


}