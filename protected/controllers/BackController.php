<?php

class BackController extends Controller
{
    public $layout='back';

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    //все запрещено всем, кроме root
    public function accessRules()
    {
        return array(
            array('deny',
                'users'=>array('?'),
            ),
            array('allow',
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