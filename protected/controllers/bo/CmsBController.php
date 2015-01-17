<?php
class CmsBController extends BackController
{
    public $layout='cms';



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
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {

        $this->render('index');
    }







}