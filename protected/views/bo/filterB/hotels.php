<?php
echo 'Отели:';
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$model->hotelDataProvider,
    'columns'=>array(
        'name',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{del}',
            'buttons'=>array
            (
                'del'=>array(
                    //в гете передается regionId
                    'url'=>'Yii::app()->createUrl("/back/filter/unbindHotel/".$data->entityId)',
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