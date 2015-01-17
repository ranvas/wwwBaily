<?php
class membershipException extends CException {}

class membershipRepository extends CComponent
{
    //region служебные

    public function createNewAccount(Account $user)
    {
        if($user->save())
        {
            $user->scenario = 'create';
            return $user->id;
        }
        throw new membershipException('не получилось создать аккаунт '.implode(' ',$user->attributes));
    }


    //endregion

    //region create


    public function createNewRole($name, $description = 'no comments')
    {
        return Yii::app()->authManager->createRole($name, $description);
    }


    /**
     * @param $parent
     * @param $child
     * @return mixed
     * @throws membershipException
     */
    public function addAuthChild($parent, $child)
    {
        if(($parent === 'root')||($child === 'root'))
        {
            throw new membershipException('несанкционированный доступ');
        }
        $auth=Yii::app()->authManager;
        $item = $auth->getAuthItem($parent);
        return $item->addChild($child);
    }


    //endregion

    //region read

    public function getAccountNamesByAuthItem($item)
    {
        //получить всех активных пользователей
        $sql = 'select id,username from Account where status = 1';
        $users = Yii::app()->db->createCommand($sql)->queryAll();
        $accessed = array();
        //кто проходит проверку, того вернуть
        foreach($users as $user)
        {
            if(Yii::app()->AuthManager->checkAccess($item,$user['id']))
            {
                $accessed[] = $user;
            }
        }
        return $accessed;
    }
    public function getAccountNameById($userId)
    {
        $sql = "select id, username from Account where id = $userId";
        return Yii::app()->db->createCommand($sql)->queryAll();
    }


    public function getAllChildNameByParentName($roleName)
    {
        $sql = 'select child from AuthItemChild where parent = \''.$roleName.'\'';
        return Yii::app()->db->createCommand($sql)->queryColumn();

    }

    /**
     * @param $id
     * @return Account
     */
    function getAccountById($id)
    {
        return Account::model()->findByPk($id);
    }

    /**
     * @param $name
     * @return Account
     */
    function getAccountByNameWhereStatusTrue($name)
    {
        return Account::model()->find('username = \''.$name.'\' and status > 0');
    }

    function getRoles()
    {
        $sql = 'select name from AuthItem where type = 2';
        $result = Yii::app()->db->createCommand($sql)->queryColumn();
        return $result;
    }

    public function getAllTasks()
    {
        $sql = 'select name,bizrule from AuthItem where type = 1';
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        return $result;
    }

    public function getAllOperations()
    {
        $sql = 'select name from AuthItem where type = 0';
        $result = Yii::app()->db->createCommand($sql)->queryColumn();
        return $result;
    }

    public function getAINameDescription($name = null)
    {
        if(isset($name))
        {
            $sql = "select name, concat(name,', ',description, ' тип=', type) AS text from AuthItem  where name not in ('".$name."','root')";
        }
        else
        {
            $sql = "select name,concat(name,', ',description, ' тип=', type) AS text from AuthItem";
        }
        return Yii::app()->db->createCommand($sql)->queryAll();
    }

    /**
     * @return Account[]
     */
    function getAllUsers()
    {
        return Account::model()->findAll();
    }

    function getUserStatus($id)
    {
        $sql = 'select status from Account where id='.$id;
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }




    //endregion

    //region update


    public function updateRole($name, $description)
    {
        $ret =Yii::app()->db->createCommand()->update('AuthItem',array('description'=>$description),array('and','name=\''.$name.'\'','type=2'));
        return $ret;
    }


    public function updateAccount(Account $account)
    {

        if(($account->role !== "root")&&($account->id !== "1"))
        {
            if($account->save())
            {
                return $account;
            }
        }
        throw new membershipException('не получилось обновить аккаунт '.implode(' ',$account->attributes));
    }

    //endregion

    //region delete
    public function deleteAccount(Account $account)
    {
        //удаляем AuthAssignment
        $sql = 'delete from AuthAssignment where userid = '.$account->id;
        Yii::app()->db->createCommand($sql)->execute();
        return $account->delete();
    }

    function deleteUserById($id)
    {
        $condition = 'role=\'user\'';
//        $params = array(':role'=>'user');
        $user = Account::model()->findByPk($id,$condition);
        return $this->deleteAccount($user);
    }
    function deleteAdminById($id)
    {
        //удалить аккаунт
        $condition = 'role=:role';
        $params = array(':role'=>'admin');
        $user = Account::model()->findByPk($id,$condition,$params);
        return $this->deleteAccount($user);
    }

    public function deleteAuthItemChildByParent($parentName)
    {
        //удалить все привязки
        return Yii::app()->db->createCommand()->delete('AuthItemChild','parent=\''.$parentName.'\'');
    }




    function deleteRole($name)
    {
        $connection = Yii::app()->db;
        $sql = 'select * from AuthItem where name = \''.$name.'\' and type = 2';
        $role = $connection->createCommand($sql)->queryRow();
        //если роль не найдена
        if($role)
        {
            //найти назначение роли пользователям
            $sql = 'select username from Account, AuthAssignment where itemname = \''.$name.'\''.' and AuthAssignment.userid = Account.id';
            $used = $connection->createCommand($sql)->queryColumn();
            if ($used)
            {
                throw new membershipException('удалить роль '.$name.' не получилось, т.к. она назначена для: '.implode(', ',$used));
            }

            $sql = 'delete from AuthItemChild where parent = \''.$name.'\'';
            $connection->createCommand($sql)->execute();
            $sql = 'delete from AuthItemChild where child = \''.$name.'\'';
            $connection->createCommand($sql)->execute();
            $sql = 'delete from AuthItem where name = \''.$name.'\'';
            $connection->createCommand($sql)->execute();
            return true;
        }
        else
        {
            throw new membershipException('попытка удалить несуществующую роль '.$name);
        }
    }


    //endregion


}