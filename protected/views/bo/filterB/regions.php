<?php
echo 'Курорты:';
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$model->regionDataProvider,
    'columns'=>array(
        'nameCyr',
        array(
            'name'=>'hotels',
            'type'=>'raw',
            'value'=>function($data) use ($model){
                $hotels = $model->getHotelsByRegionId($data->entityId);
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
        array(
            'class'=>'CButtonColumn',
            'template'=>'{del}',
            'buttons'=>array
            (
                'del'=>array(
                    //в гете передается regionId
                    'url'=>'Yii::app()->createUrl("/back/filter/unbindRegion/".$data->entityId)',
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