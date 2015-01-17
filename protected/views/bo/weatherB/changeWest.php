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
echo $form->errorSummary($model->west); ?>
    <br>
<?php echo $form->label($model->west,'id').':'; ?>
<?php echo $form->textField($model->west,'id', array('readonly'=>true)) ?>
    <br>
<?php echo $form->label($model->west,'name').':'; ?>
<?php echo $form->textField($model->west,'name') ?>
    <br>
<?php
echo $form->label($model->west,'count').':';
echo $form->textField($model->west,'count');
echo '<br>';
echo $form->label($model->west,'raw').':';
echo $form->textArea($model->west,'raw');
echo '<br>';
echo CHtml::SubmitButton("обновить");
$this->endWidget(); ?>
<?php
echo '<br>';
echo 'Привязка станций к странам и курортам: <br>';

$countryButton = CHtml::ajaxButton("привязать к стране",Yii::app()->createUrl("/back/weather/BindCountry/"),array(
    'data'=>array(
        'id'=>$model->id,
        'countryId'=>'js:$("#westVM_country option:selected").val()',
    ),
    'success'=>'function(data) {if(data=="успешно"){window.location.href=window.location.href;}else{alert(data);}}'
));
$region = Yii::app()->createUrl('back/weather/GetReg/');
$regionButton = CHtml::ajaxButton("привязать к курорту",Yii::app()->createUrl("/back/weather/BindRegion/"),array(
    'data'=>array(
        'id'=>$model->id,
        'regionId'=>'js:$("#westVM_region option:selected").val()'
    ),
    'success'=>'function(data) {if(data=="успешно"){location.reload();}else{alert(data);}}'
));

$this->widget('operatorCountryRegionDDL', array(
    'model'=>$model,
    'country'=>true,
    'region'=>Yii::app()->createUrl('/back/weather/getRegions/'.$model->id),
    'countryButton'=>$countryButton,
    'regionButton'=>$regionButton
));




echo '<br>';
echo $form->label($model->west,'countriesDataProvider').':';
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$model->countriesDataProvider,
    'columns'=>array(
        'name',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{del}',
            'buttons'=>array
            (
                'del'=>array(
                    'url'=>'Yii::app()->createUrl("/back/weather/unbindCountry/".$data->entityId)',
                    'imageUrl'=>Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/delete.png',
                    'options'=>array(
                        'confirm'=>'Ты уверен?',
                        'ajax'=>array(
                            'url'=>'js:$(this).attr("href")',
                            'data'=>array(
                                'id'=>$model->id,
                            ),
                            'success'=>'function(data) {if(data=="успешно"){location.reload();}else{alert(data);}}',

                        )
                    )
                ),
            )
        )
    ),


));
echo '<br>';
echo $form->label($model->west,'regionsDataProvider').':';
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$model->regionsDataProvider,
    'columns'=>array(
        'name',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{del}',
            'buttons'=>array
            (
                'del'=>array(
                    'url'=>'Yii::app()->createUrl("/back/weather/unbindRegion/".$data->entityId)',
                    'imageUrl'=>Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/delete.png',
                    'options'=>array(
                        'confirm'=>'Ты уверен?',
                        'ajax'=>array(
                            'url'=>'js:$(this).attr("href")',
                            'data'=>array(
                                'id'=>$model->id,
                            ),
                            'success'=>'function(data) {if(data=="успешно"){location.reload();}else{alert(data);}}',

                        )
                    )
                ),
            )
        )
    )
));

?>



<?php
echo CHtml::link('Назад', Yii::app()->createUrl('/back/weather/')); ?>