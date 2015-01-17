<?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider'=>$filters->search(),
        'columns'=>array(
            'name',
            'manager.username',
            'vision',
            array(
                'class'=>'CButtonColumn',
                'template'=>'{update}{del}',
                'buttons'=>array
                (
                    'update'=>array(
                        'url'=>'Yii::app()->createUrl("/back/filter/changeFilter/".$data->id."/")',
                        'visible'=>'((Yii::app()->user->checkAccess("FilterAdmin"))||($data->vision == 1))',
                    ),
                    'del'=>array(
                        'url'=>'Yii::app()->createUrl("/back/filter/deleteFilter/".$data->id."/")',
                        'imageUrl'=>Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/delete.png',
                        'visible'=>'((Yii::app()->user->checkAccess("FilterAdmin"))||($data->vision == 1))',
                        'options'=>array(
                            'confirm'=>'Ты уверен?',
                            'ajax'=>array(
                                'url'=>'js:$(this).attr("href")',
                                'success'=>'function(data) {if(data=="успешно"){location.reload();}else{alert(data);}}',

                            )
                        )
                    )
                )

            )
        )

    ));
echo CHtml::link('добавить фильтр', Yii::app()->createUrl('/back/filter/createFilter/'));
//кнопка добавить
