<?php
$form=$this->beginWidget('CActiveForm', array());
echo $form->errorSummary($model->filter);
echo $form->label($model->filter,'name').':';
echo CHtml::activeTextField($model->filter,'name');
echo '<BR>';
echo $form->label($model->filter,'description').':';
echo CHtml::activeTextArea($model->filter,'description');
echo '<BR>';
echo $form->label($model->filter,'manager.username').':';
echo CHtml::activeDropDownList($model->filter,'managerId',$model->managers);
echo '<BR>';
echo $form->label($model->filter,'vision').':';
echo '<BR>';
echo CHtml::activeRadioButtonList($model->filter,'vision',$model->visions);
echo '<BR>';





//region кнопка и линк
echo ($model->saved)?'Фильтр  c id = '.$model->saved.' создан':'';
echo '<BR>';
echo CHtml::SubmitButton("Создать");
$this->endWidget();
echo CHtml::link('Назад', Yii::app()->createUrl('/back/Filter/'));
//endregion
?>