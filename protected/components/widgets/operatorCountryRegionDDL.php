<?php
class operatorCountryRegionDDL extends CWidget
{
    public $model;
    public $operatorButton = false;
    public $countryButton = false;
    public $regionButton = false;
    public $hotelButton = false;
    public $operator = false;
    public $country = false;
    public $region = false;
    public $hotel = false;

    public function run()
    {
        if($this->operator)//есть операторы
        {
            echo CHtml::activeDropDownList($this->model,'operator',$this->model->operators, array(
                'prompt'=>'выбрать оператора'
            ));
            if($this->operatorButton)
            {
                echo $this->operatorButton;
            }
        }
        if($this->country)//есть страны
        {
            $countryName = MyWidgetHTML::resolveModelName($this->model, 'country');
            $countryId = MyWidgetHTML::resolveIdByName($countryName);
            if($this->region)//курорты в виде url, нужно добавить на соответствие урл
            {
                $regionName = MyWidgetHTML::resolveModelName($this->model, 'region');
                $regionId = MyWidgetHTML::resolveIdByName($regionName);
                if($this->hotel) //отели в виде url
                {
                    $hotelName = MyWidgetHTML::resolveModelName($this->model, 'hotel');
                    $hotelId = MyWidgetHTML::resolveIdByName($hotelName);
                    echo '<BR>';
                    echo CHtml::activeDropDownList($this->model,'country',$this->model->countries, array(
                        'prompt'=>'выбрать страну',
                        'ajax'=>array(
                            'type'=>'get',
                            'url'=>$this->region,
                            'update'=>'#'.$regionId,
                            'data'=>array(
                                'countryId'=>'js:$("#'.$countryId.' option:selected").val()'
                            ),
                            'beforeSend'=>'js:function() {
                                        $("#'.$hotelId.'").load("'.$this->hotel.'", {"countryId":'.'$("#'.$countryId.' option:selected").val()});
                                    }',
                        )
                    ));
                    if($this->countryButton)//есть кнопка страны
                    {
                        echo $this->countryButton;
                    }
                    echo '<BR>';
                    $this->render('regions',array('model'=>$this->model,'hotel'=>$this->hotel));
                    if($this->regionButton)//есть кнопка курорта
                    {
                        echo $this->regionButton;
                    }
                    echo '<BR>';
                    $this->render('hotels',array('model'=>$this->model));
                    if($this->hotelButton)//есть кнопка отеля
                    {
                        echo $this->hotelButton;
                    }
                }
                else//отелей нет
                {
                    echo CHtml::activeDropDownList($this->model,'country',$this->model->countries, array(
                        'prompt'=>'выбрать страну',
                        'ajax'=>array(
                            'type'=>'get',
                            'url'=>$this->region,
                            'update'=>'#'.$regionId,
                            'data'=>array(
                                'countryId'=>'js:$("#'.$countryId.' option:selected").val()'
                            ),
                        )
                    ));
                    if($this->countryButton)//есть кнопка страны
                    {
                        echo $this->countryButton;
                    }
                    echo '<BR>';
                    $this->render('regions',array('model'=>$this->model));
                    if($this->regionButton)//есть кнопка курорта
                    {
                        echo $this->regionButton;
                    }
                }
            }
            else//нет курортов
            {
                echo '<BR>';
                echo CHtml::activeDropDownList($this->model,'country',$this->model->countries, array(
                    'prompt'=>'выбрать страну'
                ));
                if($this->countryButton)//нет кнопки страны
                {
                    echo $this->countryButton;
                }
            }
        }







    }
}

