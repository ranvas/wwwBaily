<?php
$operatorButton = CHtml::ajaxButton("привязать оператора к фильтру",Yii::app()->createUrl("/back/filter/bindOperator/"),array(
    'data'=>array(
        'id'=>$model->id,
        'operatorId'=>'js:$("#filterVM_operator option:selected").val()',
    ),
    'success'=>'function(data) {if(data=="успешно"){window.location.href=window.location.href;}else{alert(data);}}'
));
$countryButton = CHtml::ajaxButton("привязать страну к фильтру",Yii::app()->createUrl("/back/filter/bindCountry/"),array(
    'data'=>array(
        'id'=>$model->id,
        'countryId'=>'js:$("#filterVM_country option:selected").val()',
    ),
    'success'=>'function(data) {if(data=="успешно"){window.location.href=window.location.href;}else{alert(data);}}'
));

$regionButton = CHtml::ajaxButton("привязать курорт к фильтру",Yii::app()->createUrl("/back/filter/BindRegion/"),array(
    'data'=>array(
        'id'=>$model->id,
        'regionId'=>'js:$("#filterVM_region option:selected").val()'
    ),
    'success'=>'function(data) {if(data=="успешно"){window.location.href=window.location.href;}else{alert(data);}}'
));

$hotelButton = CHtml::ajaxButton("привязать отель к фильтру",Yii::app()->createUrl("/back/filter/BindHotel/"),array(
    'data'=>array(
        'id'=>$model->id,
        'hotelId'=>'js:$("#filterVM_hotel option:selected").val()'
    ),
    'success'=>'function(data) {if(data=="успешно"){window.location.href=window.location.href;}else{alert(data);}}'
));

$this->widget('operatorCountryRegionDDL', array(
    'model'=>$model,
    'operator'=>true,
    'country'=>true,
    'region'=>Yii::app()->createUrl('/back/filter/getRegions/'.$model->id),
    'hotel'=>Yii::app()->createUrl('/back/filter/getHotels/'.$model->id),
    'operatorButton'=>$operatorButton,
    'countryButton'=>$countryButton,
    'regionButton'=>$regionButton,
    'hotelButton'=>$hotelButton
));