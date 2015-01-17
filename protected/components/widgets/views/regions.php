<?php
if (isset($hotel))//передана ссылка на отели
{
    $hotelName = MyWidgetHTML::resolveModelName($model, 'hotel');
    $hotelId = MyWidgetHTML::resolveIdByName($hotelName);
    $regionName = MyWidgetHTML::resolveModelName($model, 'region');
    $regionId = MyWidgetHTML::resolveIdByName($regionName);
    echo CHtml::activeDropDownList($model,'region',$model->getRegions(), array(
        'prompt'=>'выбрать курорт',
        'ajax'=>array(
            'type'=>'get',
            'url'=>$hotel,
            'update'=>'#'.$hotelId,
            'data'=>array(
                'regionId'=>'js:$("#'.$regionId.' option:selected").val()'
            )
        )
    ));
}
else
{
//    CVarDumper::dump("непонятно");
//    CVarDumper::dump($model->getRegions());
    echo CHtml::activeDropDownList($model,'region',$model->getRegions(),array('prompt'=>'выбрать курорт'));
}

?>
