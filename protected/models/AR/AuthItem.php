<?php
/**
 * Роль можно создавать, менять, удалять, назначать другим ролям. Нельзя установить бизнесправило.
 * Операции можно создавать. Нельзя менять, удалять, назначать другим операциям, изменять бизнес правила.
 * Задачи можно создавать. Нельзя менять, удалять, назначать другим операциям. Можно изменять бизнес правила.
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 * @property array $children
 */
class AuthItem extends CActiveRecord
{
    /**
     * @return string таблица соответствия в БД
     */
    public function tableName()
    {
        return 'AuthItem';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function attributeLabels()
    {
        return array(
            'name'=>'Имя'
        );

    }



}