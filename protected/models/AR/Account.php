<?php

/**
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $role
 * @property integer $status
 *
 */
class Account extends CActiveRecord
{

    public $password_repeat;
	/**
	 * @return string таблица соответствия в БД
	 */
	public function tableName()
	{
		return 'Account';
	}

	/**
	 * @return array правила валидации
	 */
	public function rules()
	{
		return array(
            //необходим всегда
            array('username,email','required'),

            //необходимы только в конкретных случаях
            array('id','required','on'=>'update'),
            array('password,password_repeat','required','on'=>'create'),

            //прочие валидации всегда
            array('email','email'),
            array('username','length','min'=>'4','max'=>'150'),
			array('status', 'numerical', 'integerOnly'=>true),
            array('role', 'roleExist'),
            array('username, email','unique'),

            //прочие валидации в конкретном случае
            array('password','compare','on'=>'create'),

		);
	}

	/**
	 * @return array связи
	 */
	public function relations()
	{
        return array(

//            'recordsAuthor'=>array(self::HAS_MANY, 'Record', 'authorId'),
//            'recordsRedactor'=>array(self::HAS_MANY, 'Record', 'redactorId'),
            'filterOwner'=>array(self::HAS_MANY, 'Filter', 'managerId'),
        );
	}

	/**
	 * @return array описание аттрибутов
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Идентификатор',
			'username' => 'Имя пользователя',
			'password' => 'Пароль',
			'email' => 'Почта',
			'role' => 'Роль',
			'status' => 'Статус',
            'password_repeat'=>'Подтверждение пароля'
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('status',$this->status);
        $criteria->addNotInCondition('username',array('root'));
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,

		));
	}

    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

//    /**
//     * @return bool
//     * действия до сохранения модели, но ПОСЛЕ валидации
//     */
//    public function beforeSave() {
//        $this->password = crypt($this->password, CPasswordHelper::generateSalt());
//        return parent::beforeSave();
//    }

    public function afterDelete()
    {
        $assignments = Yii::app()->authManager->getAuthAssignments($this->id);
        if (!empty($assignments))
        {
            foreach ($assignments as $key => $assignment)
            {
                Yii::app()->authManager->revoke($key, $this->id);
            }
        }
        return parent::afterDelete();
    }


    /**
     * Действия после сохранения модели
     */
    public function afterSave()
    {
        $assignments = Yii::app()->authManager->getAuthAssignments($this->id);
        if (!empty($assignments))
        {
            foreach ($assignments as $key => $assignment)
            {
                Yii::app()->authManager->revoke($key, $this->id);
            }
        }
        Yii::app()->authManager->assign($this->role, $this->id);
        return parent::afterSave();
    }

    public function getRoles()
    {
        if(Yii::app()->user->checkAccess('boRoles'))
        {
            return functions::compactList(Yii::app()->dm->membership->getRoles());
        }
        else
        {
            throw new membershipException('несанкционированный доступ');
        }
    }

    public function roleExist($attribute)
    {
        if(!( in_array($this->role, Yii::app()->dm->membership->getRoles())))
        {
            $this->addError($attribute,'Неизвестная роль '.$this->username);
            throw new membershipException('Неизвестная роль');

        }
    }




}
