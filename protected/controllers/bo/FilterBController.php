<?php
class FilterBController extends CmsBController
{

    //правила доступа для этого контроллера
    public function accessRules()
    {
        return array(
            array('deny',
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('index','getRegions','getHotels','createFilter', 'BindOperator','UnbindOperator','BindCountry','UnbindCountry','BindRegion','UnbindRegion','deleteFilter','changeFilter','BindHotel','UnbindHotel'),
                'roles'=>array('boFilters'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $filters = new Filter('view');
        $this->render('index',compact('filters'));
    }

    public function actionChangeFilter()
    {
        $model = new filterVM();
        $model->id = $_REQUEST["alias"];
        if(isset($_REQUEST['Filter']))
        {
            $model->filter->setAttributes($_REQUEST['Filter']);
            if($model->filter->validate())
            {
                $model->saved = Yii::app()->dm->searchTour->updateFilter($model->filter->id,$model->filter->name,$model->filter->managerId, $model->filter->vision, $model->filter->description);
            }
        }
        $this->render('changeFilter',compact('model'));
    }

    public function actionDeleteFilter()
    {
         $transaction = Yii::app()->db->beginTransaction();
         try
         {
             $filterId = $_REQUEST['alias'];
             Yii::app()->dm->searchTour->unbindFilterFromAll($filterId);
             Yii::app()->dm->searchTour->deleteFilterById($filterId);
             $transaction->commit();
             $this->returnAjax("успешно");
         }
         catch(Exception $ex)
         {
             $transaction->rollback();
             $this->returnAjax($ex->getMessage());
         }
    }

    public function actionCreateFilter()
    {
        $model = new filterVM('create');
        $model->saved = false;
        if(isset($_REQUEST['Filter']))
        {
            $model->filter->setAttributes($_REQUEST['Filter']);
            if($model->filter->validate())
            {
                $model->saved = Yii::app()->dm->searchTour->createNewFilter($model->filter->name, $model->filter->managerId,$model->filter->vision, $model->filter->description)->id;
            }
        }
        else
        {
            $model->filter->managerId = Yii::app()->user->id;
        }
        $this->render('createFilter',compact('created','model'));
    }

    public function actionBindOperator()
    {
        $operatorId = $_GET['operatorId'];
        if($operatorId)
        {
            $filterId = $_GET['id'];
            try
            {
                if(Yii::app()->dm->searchTour->bindFilterToOperator($filterId, $operatorId))
                {
                    $this->returnAjax('успешно');
                }
            }
            catch(CException $ex)
            {
                $this->returnAjax($ex->getMessage());
            }
        }
        else
        {
            $this->returnAjax('нечего прикреплять');
        }
    }

    public function actionUnbindOperator()
    {
        $transaction= Yii::app()->db->beginTransaction();
        try
        {
            $operatorId = $_GET['alias'];
            $filterId = $_GET['id'];
            Yii::app()->dm->searchTour->unbindFilterFromOperator($filterId, $operatorId);
            Yii::app()->dm->searchTour->unbindFilterFromFreeCountries($filterId);
            Yii::app()->dm->searchTour->unbindFilterFromFreeRegions($filterId);
            Yii::app()->dm->searchTour->unbindFilterFromFreeHotels($filterId);
            $transaction->commit();
            $this->returnAjax("успешно");

        }
        catch(CException $ex)
        {
            $transaction->rollback();
            $this->returnAjax($ex->getMessage());
        }
    }

    public function actionBindCountry()
    {
        $countryId = $_GET['countryId'];
        if($countryId)
        {
            $filterId = $_GET['id'];
            try
            {
                if(Yii::app()->dm->searchTour->bindFilterToCountry($filterId, $countryId))
                {
                    $this->returnAjax('успешно');

                }
            }
            catch(CDbException $ex)
            {
                $this->returnAjax("Страна уже привязана");
            }
            catch(Exception $ex)
            {
                $this->returnAjax($ex->getMessage());
            }
        }
        else
        {
            $this->returnAjax('нечего прикреплять');
        }
    }

    public function actionUnbindCountry()
    {
        $transaction=Yii::app()->db->beginTransaction();
        try
        {
            $countryId = $_GET['alias'];
            $filterId = $_GET['id'];
            Yii::app()->dm->searchTour->unbindFilterFromCountry($filterId, $countryId);
            Yii::app()->dm->searchTour->unbindFilterFromFreeRegions($filterId);
            Yii::app()->dm->searchTour->unbindFilterFromFreeHotels($filterId);
            $transaction->commit();
            $this->returnAjax("успешно");
        }
        catch(Exception $ex)
        {
            $transaction->rollback();
            $this->returnAjax($ex->getMessage());
        }
    }

    public function actionBindRegion()
    {
        $regionId = $_GET['regionId'];
        if($regionId)
        {
            $filterId = $_GET['id'];
            //проверить, что страна, к которой принадлежит курорт привязана к фильтру
            if(Yii::app()->dm->searchTour->checkCountryByFilterIdAndEntityId($filterId,$regionId))
            {
                try
                {
                    Yii::app()->dm->searchTour->bindFilterToRegion($filterId, $regionId);
                    $this->returnAjax('успешно');
                }
                catch(CDbException $ex)
                {
                    $this->returnAjax("Курорт уже привязан");
                }
                catch(Exception $ex)
                {
                    $this->returnAjax($ex->getMessage());
                }
            }
            else
            {
                $this->returnAjax('Перед прикреплением курорта необходимо прикрепить страну');
            }
        }
        else
        {
            $this->returnAjax('нечего прикреплять');
        }
    }

    public function actionUnbindRegion()
    {
        $regionId = $_GET['alias'];
        $filterId = $_GET['id'];
        try
        {
            Yii::app()->dm->searchTour->unbindFilterFromRegion($filterId, $regionId);
            $this->returnAjax("успешно");
        }
        catch(CException $ex)
        {
            $this->returnAjax($ex->getMessage());
        }
    }

    public function actionBindHotel()
    {
        $hotelId = $_GET['hotelId'];
        if($hotelId)
        {
            $filterId = $_GET['id'];
            //проверить, что страна, к которой принадлежит отель привязана к фильтру
            if(Yii::app()->dm->searchTour->checkCountryByFilterIdAndEntityId($filterId,$hotelId))
            {
                try
                {
                    Yii::app()->dm->searchTour->bindFilterToHotel($filterId, $hotelId);
                    $this->returnAjax('успешно');
                }
                catch(CDbException $ex)
                {
                    $this->returnAjax("Отель уже привязан");
                }
                catch(Exception $ex)
                {
                    $this->returnAjax($ex->getMessage());
                }
            }
            else
            {
                $this->returnAjax('Перед прикреплением отеля необходимо прикрепить страну');
            }
        }
        else
        {
            $this->returnAjax('нечего прикреплять');
        }
    }

    public function actionUnbindHotel()
    {
        $hotelId = $_GET['alias'];
        $filterId = $_GET['id'];
        try
        {
            if(Yii::app()->dm->searchTour->unbindFilterFromHotel($filterId, $hotelId))
            {
                $this->returnAjax("успешно");
            }
        }
        catch(CException $ex)
        {
            $this->returnAjax($ex->getMessage());
        }
    }



    //region widget
    public function actionGetRegions()
    {
        CVarDumper::dump($_REQUEST);
        if((isset($_REQUEST['countryId']))&&(isset($_GET['alias'])))
        {
            $model = new filterVM();
            $model->country = $_REQUEST['countryId'];//id страны
            $model->id = $_GET['alias'];//id фильтра
            $hotel = Yii::app()->createUrl("/back/filter/getHotels"); //ссылка на смену отелей
            $this->renderPartial('application.components.widgets.views.regions',array('model'=>$model,'hotel'=>$hotel));//view использует метод модели
        }
    }

    public function actionGetHotels()
    {
        if(isset($_GET['alias']))
        {
            $model = new filterVM();
            $model->id = $_GET['alias'];//id фильтра
            if(isset($_REQUEST['countryId']))
            {
                $model->country = $_REQUEST['countryId'];//id страны
            }
            elseif(isset($_REQUEST['regionId']))
            {
                $model->region = $_REQUEST['regionId'];//id курорта
            }
            $this->renderPartial('application.components.widgets.views.hotels',compact('model'));//view использует метод модели
        }
    }
    //endregion
}