<?php
class APIBController extends CmsBController
{




    //правила доступа для этого контроллера
    public function accessRules()
    {
        return array(
            array('deny',
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('index','updateFunctions','UpdateFunction'),
                'roles'=>array('boAPI'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $model = new functionVM();
        $this->render('index', compact('model'));
    }


    public function actionUpdateFunctions()
    {
        if(Yii::app()->dm->api->updateFunctions() > 0)
        {
            $this->returnAjax("успешно");
        }
        else
        {
            $this->returnAjax("Баста, карапузики, нечего обноволять");
        }
    }

    public function actionUpdateFunction()
    {
        $return = "необходимо выбрать функцию";
        if(isset($_POST['functionVM']))
        {
            $model = new functionVM('update');
            $model->setAttributes($_POST['functionVM']);
            if($model->validate())
            {

                $api = Yii::app()->dm->api;
                try
                {
                switch($model->functionName)
                {
                    case "getHotels":
                        if($model->functionParams != "")
                        {
                            $return = $api->updateHotelsByCountryId($model->functionParams);
                        }
                        else
                        {
                            $return = $api->updateHotels();
                        }
                        break;
                    case "getResults":
                        $return = $this->getResults($model);
                        break;
                    case "getCountries":
                        $return = $api->updateCountries();
                        break;
                    case "getCurrencies":
                        $return = $api->updateCurrencies();
                        break;
                    case "getRegions":
                        $return = $api->updateRegions();
                        break;
                    case "getCategories":
                        $return = $api->updateCategories();
                        break;
                    case "getMeals":
                        $return = $api->updateMeals();
                        break;
                    case "getOperators":
                        $return = $api->updateOperators();
                        break;
                    default:
                        $return = "функция не поддерживается";
                        break;
                }
                }
                catch(APIException $e)
                {
                    $return = $e->getMessage();
                }
            }
            else
            {
                $return = $model->getErrors();
            }
        }
        $this->returnAjax($return);
    }

    private function getResults($model)
    {
        $model->scenario = "results";
        if ($model->validate())
        {
            try
            {
                $params = explode(',',$model->functionParams);
                return Yii::app()->dm->api->getResults($params, true);
            }
            catch(Exception $ex)
            {
                return $ex->getMessage();
            }

        }
        else
        {
            return "не ок";
        }
    }



}