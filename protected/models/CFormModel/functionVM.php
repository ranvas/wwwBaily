<?php
class functionVM extends CFormModel
{
    public $functionName;
    public $functionParams;

    public function rules()
    {
        return array(
            array('functionName','required'),
            array('functionName','length','min'=>'4','max'=>'150'),
            array('functionParams','safe', 'on'=>'update'),
            array('functionParams','required', 'on'=>'results'),
            array('functionParams','length','min'=>'1','max'=>'255','on'=>'results'),
        );
    }
    public function attributeLabels()
    {
        return array(

        );
    }

    /**
     * получить CArrayDataProvider для CGridView
     */
    public function getEagerFunctions()
    {
        $rawData = Yii::app()->dm->api->getEagerFunctions();
        $dataProvider = new CArrayDataProvider($rawData, array(
            'keyField'=>'name'
        ));
        return $dataProvider;
    }

    /**
     * получить список функций для ddl
     */
    public function getFunctions()
    {
        $functions = functions::compactList(Yii::app()->dm->api->getFunctionsNames());
        return $functions;
    }





}