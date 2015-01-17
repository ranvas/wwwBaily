<?php $form=$this->beginWidget('CActiveForm', array());
echo $form->errorSummary($model); ?>
<br>
<?php echo $form->label($model,'username').':'; ?>
<?php echo $form->textField($model,'username') ?>
<br>
<?php
if($model->scenario === 'create')
{
    echo $form->label($model,'password').':';
    echo $form->passwordField($model,'password');
    echo '<br>';
    echo $form->label($model,'password_repeat').':';
    echo $form->passwordField($model,'password_repeat');
    echo '<br>';
}
else
{
    echo $form->label($model,'password').':';
    echo $form->textField($model,'password');
    echo '<br>';
}
 ?>

<?php echo $form->label($model,'email').':'; ?>
<?php echo $form->emailField($model,'email'); ?>
<br>
<?php
if ($model->scenario === "update")
{
    echo ($created)?'Пользователь c id = '.$created.' обновлен':'';
    $message = "Обновить";
}
else
{
    echo ($created)?'Пользователь создан c id = '.$created:'';
    $message = "Создать";
}
echo '<br>';

echo '<br>';
echo CHtml::SubmitButton($message);
?>


<?php $this->endWidget(); ?>



<?php
echo CHtml::link('Назад', Yii::app()->createUrl('/back/membership/')); ?>