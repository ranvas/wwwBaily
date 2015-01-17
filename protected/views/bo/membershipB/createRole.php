<?php
if($created)
{
    echo "Успешно";
}
$form=$this->beginWidget('CActiveForm', array());
echo $form->errorSummary($model); ?>
<br>
<?php echo $form->label($model,'name').':'; ?>
<?php echo $form->textField($model,'name') ?>
<br>
<?php echo $form->label($model,'description').':'; ?>
<?php echo $form->textArea($model,'description'); ?>
<br>
<br>
<?php echo $form->label($model,'data').':'; ?>
<?php echo $form->textField($model,'data'); ?>
<br>
    <br>
<?php echo $form->label($model,'children').':'; ?>
    <br>
<?php echo CHtml::activeCheckBoxList($model, 'children', functions::compactList(Yii::app()->dm->membership->getAINameDescription($model->name),'name','text') ,
    array(
        'uncheckValue'=>'user'
    ));

echo '<br>';
echo 'Правила типа 2 - это роли, их можно удалять, менять, и т.д. <br>Правила типа 0 - это операции, их можно создавать только программно. <br> Правила типа 1 - это операции с некоторым бизнес-правилом. Их пока лучше не трогать, пока не будет удобного описания бизнес-логики.';
echo '<br>';
if($model->scenario === 'create')
{
    echo CHtml::SubmitButton("создать");
}
elseif($model->scenario === 'update')
{
    echo CHtml::SubmitButton("обновить");
}


    ?>

<?php $this->endWidget(); ?>

<?php
echo CHtml::link('Назад', Yii::app()->createUrl('/back/membership/')); ?>