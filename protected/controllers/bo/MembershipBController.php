<?php
class MembershipBController extends CmsBController
{
    //правила доступа для этого контроллера
    public function accessRules()
    {
        return array(
            array('deny',
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('index'),
                'roles'=>array('boAdmin'),
            ),
            array('allow',
                'actions'=>array('createUser','createAdmin','createManager','changeAccount'),
                'roles'=>array('boAccounts'),
            ),
            array('allow',
                'actions'=>array('CreateRole','ChangeRole','DeleteRole'),
                'roles'=>array('boRoles'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }


    /**
     * Показать все
     */
    public function actionIndex()
    {
        $accounts = new Account("view");
        $roles = new Role("view");

        $this->render('index', compact('accounts','roles'));
    }


    public function actionCreateUser()
    {
        $model = new Account('create');
        $created = false;
        if(isset($_POST['Account']))
        {
            $model->setAttributes($_POST['Account']);
            $model->role = 'user';
            $model->status = '1';
            if ($model->validate())
            {
                $created = Yii::app()->dm->membership->createNewAccount($model);
            }
        }
        $this->render('createAccount',compact('model', 'created'));
    }

    public function actionCreateAdmin()
    {
        $model = new Account('create');
        $created = false;
        if(isset($_POST['Account']))
        {
            $model->setAttributes($_POST['Account']);
            $model->role = 'admin';
            $model->status = '1';
            if ($model->validate())
            {
                $created = Yii::app()->dm->membership->createNewAccount($model);
            }
        }
        $this->render('createAccount',compact('model', 'created'));
    }

    public function actionCreateManager()
    {
        $model = new Account('create');
        $created = false;
        if(isset($_POST['Account']))
        {
            $model->setAttributes($_POST['Account']);
            $model->role = 'manager';
            $model->status = '1';
            if ($model->validate())
            {
                $created = Yii::app()->dm->membership->createNewAccount($model);
            }
        }
        $this->render('createAccount',compact('model', 'created'));
    }



    public function actionChangeAccount($alias=0)
    {
        if (Yii::app()->request->isAjaxRequest)
        {
            $model = Account::model()->findByPk($_POST["id"]);

            $model->setAttributes($_POST);
            if($model->validate())
            {
                $model->isNewRecord = false;
                Yii::app()->dm->membership->updateAccount($model);
                $this->returnAjax("успешно");
            }
            else
            {
                $this->returnAjax($model->getErrors());
            }
        }
        else
        {
            $model = Account::model()->findByPk($alias);
            $created = false;
            if(isset($_POST['Account']))
            {
                $model->setAttributes($_POST['Account']);
                if ($model->validate())
                {
                    $created = Yii::app()->dm->membership->updateAccount($model)->id;

                }
            }
            $this->render('createAccount',compact('model', 'created'));
        }
    }

    public function actionCreateRole()
    {
        $created = false;
        $model = new Role("create");
        if(isset($_REQUEST['Role']))
        {
            $model->setAttributes($_REQUEST['Role']);
            if($model->validate())
            {
                $created = Yii::app()->dm->membership->createNewRole($model->name, $model->description);
                if (is_array($model->children))
                {
                    try
                    {
                        foreach($model->children as $child)
                        {
                            if ($model->name === $child)
                            {
                                $created = false;
                            }
                            else
                            {
                                Yii::app()->dm->membership->addAuthChild($model->name, $child);
                            }
                        }
                    }
                    catch(CException $e)
                    {
                        $created = false;
                    }
                }
            }
        }
        $this->render("createRole", compact('model','created'));
    }

    public function actionChangeRole($alias)
    {
        $created = false;
        $model = Role::model()->findByPk($alias);
        if(isset($_REQUEST['Role']))
        {
            $model->isNewRecord = false;
            $model->setAttributes($_REQUEST['Role']);
            if ($model->validate())
            {
                try
                {
                    //обновить имя
                    Yii::app()->dm->membership->updateRole($model->name, $model->description);
                    //удалить привязки
                    Yii::app()->dm->membership->deleteAuthItemChildByParent($model->name);
                    if(is_array($model->children))
                    {
                        //проставить привязки
                        foreach($model->children as $child)
                        {
                            $created = ((Yii::app()->dm->membership->addAuthChild($model->name, $child))&&($created));
                        }
                    }
                }
                catch(CException $e)
                {
                    $created = false;
                }
            }
        }
        $this->render("createRole", compact('model','created'));
    }

    public function actionDeleteRole($alias)
    {
        try
        {
            Yii::app()->dm->membership->deleteRole($alias);
            $this->returnAjax("успешно");
        }
        catch(CException $e)
        {
            $this->returnAjax($e->getMessage());
        }

    }


}