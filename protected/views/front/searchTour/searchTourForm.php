<?php
$form = $this->beginWidget('CActiveForm', array(
    'id'=>'stf_'.$model->filterId,
));
?>

<?php
//Список стран
echo $form->dropDownList($model->cacheSearch,'countryKey', $model->countries, array(
    'onChange'=>'$("#'.$model->updateId.'_'.$model->filterId.'").load("'.$model->changeAction.'filter/'.$model->filterId.'/country/" + this.value + "/")'

));
?>

    <br>



<?php


//дата вылета
$this->widget('RangeDatePicker',array(
    'model'         => $model->cacheSearch,
    'language'      => 'ru',
    'attributeFrom' => 'bDate1',
    'attributeTo'   => 'bDate2',
));


?>

    <br>
<?php
//количество ночей
$this->widget('RangeDropDownList', array(
    'model'         => $model->cacheSearch,
    'attributeFrom' => 'durationMin',
    'attributeTo'   => 'durationMax',
    'valuesFrom'    => $model->nights,
    'valuesTo'      => $model->nights,
));

?>
    <br>
<?php
//курорты
echo $form->checkBoxList($model->cacheSearch, 'destQryStr', $model->regions);
?>

    <br>
<?php
//категории
echo $form->checkBoxList($model->cacheSearch, 'catQryStr', $model->categories);
?>

    <br>
<?php
//питание
echo $form->checkBoxList($model->cacheSearch, 'mealQryStr', $model->meals);
?>

    <br>
<?php
//Количество взрослых
echo $form->dropDownList($model->cacheSearch, 'adQty', $model->AdQty);
?>

    <br>

<?php
//Количество детей
echo $form->dropDownList($model->cacheSearch, 'chQty', $model->ChQty, array(
    'onChange'=>'
                switch(this.value)
                {
                   case \'0\':
                      $("#fstChd").hide();
                      $("#sndChd").hide();
                      break;
                   case \'1\':
                      $("#fstChd").show();
                      $("#sndChd").hide();
                      break;
                   case \'2\':
                      $("#fstChd").show();
                      $("#sndChd").show();
                   break;
                }'
));
?>
<div id = "fstChd" hidden="hidden">
<?php
//возраст первого ребенка
echo $form->dropDownList($model->cacheSearch, 'fstChd', $model->Chd);
?>
</div>
<div id = "sndChd" hidden="hidden">
<?php
//возраст второго ребенка
echo $form->dropDownList($model->cacheSearch, 'sndChd', $model->Chd);
?>
</div>
    <br>
<?php
//топливный сбор
echo $form->checkBox($model->cacheSearch,'airCharge');

?>
    <br>
<?php
//отели
$this->widget('hotelsCheckBoxList', array(
    'model'=>$model->cacheSearch,
    'attribute'=>'hotelQryStr',
    'data'=>$model->hotels,
    'prefixId'=>'hw'.$model->filterId
));

?>




<?php

echo CHtml::ajaxSubmitButton('ПОИСК',$model->searchAction, array(
    'update'=>'#result'
));

$this->endWidget();




