<?php
$f = $this->beginWidget('CActiveForm', array(
        'action'=> $this->createUrl('test/Result'),
        'id'=>'searchForm'
    ));

echo $f->dropDownList($searchModel->cacheSearch,'countryCode',$searchModel->countries,array(
    'prompt'=>'Select location',
    'submit'=>$this->createUrl('test/index'),
));

$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'model'=>$searchModel->cacheSearch,
    'attribute'=>'bDate1',
    'language'=>'ru',
    'options'=>array(
        'dateFormat'=>'dd.mm.yy',
        'showAnim'=>'fold',
    ),
));

echo CHtml::submitButton('поиск');

$this->endWidget();

//CVarDumper::dump($searchForm->model);






?>
