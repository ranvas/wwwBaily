<?php
class SqlBController extends CmsBController
{

    //правила доступа для этого контроллера
    public function accessRules()
    {
        return array(
            array('deny',
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('index','TruncateTables','ShowTable'),
                'roles'=>array('boSQL'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $modules['membership'] =  Yii::app()->dm->sql->getTables('membership');
        $modules['api'] = Yii::app()->dm->sql->getTables('api');
        $modules['help'] = Yii::app()->dm->sql->getTables('help');
        $modules['search'] = Yii::app()->dm->sql->getTables('search');
        $this->render('index', compact('modules'));
    }

    public function actionTruncateTables($alias)
    {
        if(Yii::app()->dm->sql->truncateTables($alias))
        {
            if(Yii::app()->request->isAjaxRequest)
            {
                $this->returnAjax('Таблицы созданы');
            }
            else
            {
                $this->redirect('/');
            }
        }
        else
        {
            $this->returnAjax('Таблицы не созданы');
        }
    }

    public function actionShowTable()
    {


        if($_POST['select'])
        {
            $select = $_POST['select'];
        }
        else
        {
            $select = '*';
        }
        $from = $_POST['from'];
        $where =  $_POST['where'];
        if (Yii::app()->dm->sql->tableExist($from))
        {
            try
            {
                $ret = Yii::app()->dm->sql->queryAll($select,$from,$where);
                $this->returnAjax($ret);
            }
            catch(SQLException $s)
            {
                $this->returnAjax($s->getMessage());
            }
            catch(Exception $e)
            {
                $this->returnAjax('fatal error');
            }


        }
        else
        {
            $this->returnAjax('Нет такой таблицы');
        }

    }

}