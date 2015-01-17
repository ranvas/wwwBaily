<?php
//кнопка обновить функции
echo CHtml::ajaxButton("обновить сигнатуры функций",
    Yii::app()->createUrl("/back/api/updateFunctions"),
    array(
        'update'=>"#afud",
        'success'=>'function(data) {if(data=="успешно"){location.reload();}else{alert(data);}}',
        'beforeSend'=>'function(){$("#afud").text("Загрузка...");}',
    ),

    array(
        'confirm'=>'Точно обновить?',
        )

);
?>

<div id="afud"></div>
<br>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'user-form',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
    'focus'=>array($model,'functionName'),
));


echo CHtml::activeDropDownList(
    $model,
    'functionName',
    $model->functions,
    array()
);
echo CHtml::activeTextField($model,'functionParams');
echo CHtml::ajaxSubmitButton("обновить",
    Yii::app()->createUrl("/back/api/updateFunction"),
    array(
        'update'=>"#fud",
        'beforeSend'=>'function(){$("#fud").text("Загрузка...");}',
    ),
    array()
);
$this->endWidget(); ?>

<div id="fud"></div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$model->eagerFunctions,
    'columns'=>array(
        'name',
        'function',
        'description',
        'inParams',
        'outParams'

    )
));


?>