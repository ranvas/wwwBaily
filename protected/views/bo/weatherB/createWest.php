<?php
if($created)
{
    echo "Успешно";
}
?>
    <br>
    <a href="http://www.wunderground.com/weather/api/d/docs?d=resources/country-to-iso-matching">линк на все коды стран</a>
    <a href="http://api.wunderground.com/api/70985b6239051976/geolookup/q/EG.json">линк на все станции страны с кодом EG</a>
    <br>
<?php
$form=$this->beginWidget('CActiveForm', array());
echo $form->errorSummary($model); ?>
<br>
<?php echo $form->label($model,'id').':'; ?>
<?php echo $form->textField($model,'id') ?>
<br>
<?php echo $form->label($model,'name').':'; ?>
<?php echo $form->textField($model,'name') ?>
<br>
<?php
echo CHtml::SubmitButton("создать");
?>

<?php $this->endWidget(); ?>

<?php
echo CHtml::link('Назад', Yii::app()->createUrl('/back/weather/')); ?>