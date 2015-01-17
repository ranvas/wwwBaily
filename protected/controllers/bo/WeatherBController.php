<?php
class WeatherBController extends CmsBController
{

    //правила доступа для этого контроллера
    public function accessRules()
    {
        return array(
            array('deny',
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('index','CreateWest','DeleteWest','ChangeWest','RefreshWest','GetRegions','BindCountry','BindRegion','unbindCountry','unbindRegion'),
                'roles'=>array('boWeather'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $weather = new West("view");
        $this->render("index",compact("weather"));
    }

    public function actionCreateWest()
    {
        $created = false;
        $model = new West('create');
        if(isset($_REQUEST['West']))
        {
            $model->setAttributes($_REQUEST['West']);
            $model->count = Yii::app()->dm->wunder->getTodayCount();
            if($model->validate())
            {
                $created = Yii::app()->dm->help->createNewWest($model->id, $model->name,$model->count);
            }
        }
        $this->render("createWest", compact('model','created'));
    }


    public function actionDeleteWest()
    {
        if(isset($_GET['alias']))
        {
            $id = $_GET['alias'];
            if(Yii::app()->dm->help->unbindWestFromAll($id))
            {
                //удалить погодную станцию
                if(Yii::app()->dm->help->deleteWestById($_GET['alias']))
                {
                    $this->returnAjax("успешно");
                }
            }
        }
    }

    public function actionChangeWest($alias=0)
    {
        $created = false;
        $model = new westVM();
        $model->id = $alias;
        if(isset($_REQUEST['West']))
        {
            $model->west->setAttributes($_REQUEST["West"]);
            if($model->west->validate())
            {
                $created = Yii::app()->dm->help->updateWest($model->west);
            }
        }
        $this->render("changeWest", compact('model','created'));
    }

    public function actionGetReg()
    {
        if(isset($_GET['id']))
        {
            $model = new westVM();
            $model->country = $_GET['id'];
            $this->renderPartial('widgets',compact('model'));
        }
    }


    public function actionBindCountry()
    {
        $message = "не выбрана страна";
        $id = $_GET['id'];
        $countryId = $_GET['countryId'];
        if($countryId != '')
        {
            try
            {
                if(Yii::app()->dm->help->bindWestToCountry($countryId, $id) > 0)
                {
                    $message = "успешно";
                }
            }
            catch(CDbException $ex)
            {
                $message = "Для этой страны уже назначена погодная станция или другая CDbException";
            }

        }
        $this->returnAjax($message);
    }

    public function actionBindRegion()
    {
        $message = "не выбран курорт";
        $id = $_GET['id'];
        $regionId = $_GET['regionId'];
        if($regionId != '')
        {
            try
            {
                if(Yii::app()->dm->help->bindWestToRegion($regionId, $id) > 0)
                {
                    $message = "успешно";
                }
            }
            catch(CDbException $ex)
            {
                $message = "Для этого курорта уже назначена погодная станция или другая CDbException";
            }

        }
        $this->returnAjax($message);
    }

    /**
     * alias - это entityId
     */
    public function actionUnbindCountry()
    {
        if(isset($_GET['alias']))
        {
            if(isset($_GET['id']))
            {
                $countryId = $_GET['alias'];
                $westId = $_GET['id'];
                if(Yii::app()->dm->help->unbindWestFromCountry($countryId, $westId) > 0)
                {
                    $this->returnAjax("успешно");
                }
            }
        }

    }

    /**
     * alias - это entityId
     */
    public function actionUnbindRegion()
    {
        if(isset($_GET['alias']))
        {
            if(isset($_GET['id']))
            {
                $regionId = $_GET['alias'];
                $westId = $_GET['id'];
                if(Yii::app()->dm->help->unbindWestFromRegion($regionId, $westId) > 0)
                {
                    $this->returnAjax("успешно");
                }
            }
        }
    }

    public function actionRefreshWest($alias=0)
    {
        Yii::app()->dm->wunder->updateStation($alias);
        $this->returnAjax("успешно");
    }

    //region widget
    public function actionGetRegions()
    {
        if(isset($_GET['countryId']))
        {
            $model = new westVM();
            $model->country = $_GET['countryId'];
            $this->renderPartial('application.components.widgets.views.regions',compact('model'));//view использует метод модели
        }
    }
    //endregion


}