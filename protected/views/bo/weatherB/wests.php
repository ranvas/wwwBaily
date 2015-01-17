<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$weather->search(),
    'columns'=>array(
        'id',
        'name',
        'count',
        array(
            'name'=>'lastUpdate',
            'type'=>'raw',
            'value'=>'date("d M Y G:i", $data->lastUpdate)'
        ),
        array(
            'name'=>'температура',
            'type'=>'raw',
            'value'=>'$data->temperature'
        ),
        array(
            'name'=>'погода',
            'type'=>'raw',
            'value'=>'$data->weather'
        ),
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}|{del}|{refresh}',
            'buttons'=>array
            (
                'update'=>array(
                    'url'=>'Yii::app()->createUrl("/back/weather/changeWest/".$data->id)',
                ),
                'del'=>array(
                    'url'=>'Yii::app()->createUrl("/back/weather/deleteWest/".$data->id)',
                    'imageUrl'=>Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/delete.png',
                    'options'=>array(
                        'confirm'=>'Ты уверен?',
                        'ajax'=>array(
                            'url'=>'js:$(this).attr("href")',
                            'success'=>'function(data) {if(data=="успешно"){location.reload();}else{alert(data);}}',

                        )
                    )
                ),
                'refresh'=>array(
                    'url'=>'Yii::app()->createUrl("/back/weather/refreshWest/".$data->id)',
                    'options'=>array(
                        'confirm'=>'Количество любых обновлений не должно превышать 10 в минуту!!!',
                        'ajax'=>array(
                            'url'=>'js:$(this).attr("href")',
                            'success'=>'function(data) {if(data=="успешно"){location.reload();}else{alert(data);}}',

                        )
                    )
                )
            )

        )


    ),
));


echo CHtml::link('добавить погодную станцию', Yii::app()->createUrl('/back/weather/createWest/'));
