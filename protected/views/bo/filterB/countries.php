<?php
echo 'Страны:';
$this->widget('zii.widgets.grid.CGridView', array(

    'dataProvider'=>$model->countryDataProvider,
    'columns'=>array(
        'name',
        //привязанные курорты
        array(
            'name'=>'regions',
            'type'=>'raw',
            'value'=>function($data) use ($model){
                $regions = $model->getRegionsByCountryId($data->entityId);
                if($regions)
                {
                    return implode(", ",functions::compactList($regions,'entityId','nameCyr'));
                }
                else
                {
                    return "все курорты";
                }
            }
        ),
        //привязанные отели
        array(
            'name'=>'hotels',
            'type'=>'raw',
            'value'=>function($data) use ($model){
                $hotels = $model->getHotelsByCountryId($data->entityId);
                if($hotels)
                {
                    return implode(", ",functions::compactList($hotels,'entityId','name'));
                }
                else
                {
                    return "все отели";
                }
            }
        ),

        //кнопки
        array(
            'class'=>'CButtonColumn',
            'template'=>'{del}',
            'buttons'=>array
            (
                'del'=>array(
                    //в гете передается countryId
                    'url'=>'Yii::app()->createUrl("/back/filter/unbindCountry/".$data->entityId)',
                    'imageUrl'=>Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/delete.png',
                    'options'=>array(
                        'confirm'=>'Ты уверен?',
                        'ajax'=>array(
                            'url'=>'js:$(this).attr("href")',
                            'data'=>array(
                                //в посте передается filterId
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