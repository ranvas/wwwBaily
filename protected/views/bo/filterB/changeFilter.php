<?php
$form=$this->beginWidget('CActiveForm', array());
echo $form->errorSummary($model->filter);
//echo CHtml::activeHiddenField($model,'id');
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
echo ($model->saved)?'Фильтр c id = '.$model->id.' обновлен':'';
echo '<BR>';
echo CHtml::SubmitButton("Обновить");
$this->endWidget();
//endregion
//region привязки
$this->renderPartial('ocrhWidget', array('model'=>$model));
echo '<br>';
$this->renderPartial('operators',array('model'=>$model));
$this->renderPartial('countries',array('model'=>$model));
$this->renderPartial('regions', array('model'=>$model));
$this->renderPartial('hotels', array('model'=>$model));
//endregion
//region привязанные курорты

//endregion


echo CHtml::link('Назад', Yii::app()->createUrl('/back/Filter/'));
?>