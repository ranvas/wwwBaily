<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$roles->search(),
    'id'=>'rlGV',
    'columns'=>array(
        'name',
        'description',
        'data',
        array(
            'name'=>'children',
            'type'=>'raw',
            'value'=>'implode(", ",$data->children)'
        ),
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}|{del}',
            'buttons'=>array
            (
                'update'=>array(
                    'url'=>'Yii::app()->createUrl("/back/membership/changeRole/".$data->name)',
                ),
                'del'=>array(
                    'url'=>'Yii::app()->createUrl("/back/membership/deleteRole/".$data->name)',
                    'imageUrl'=>Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/delete.png',
                    'options'=>array(
                        'confirm'=>'Ты уверен?',
                        'ajax'=>array(
                            'url'=>'js:$(this).attr("href")',
                            'success'=>'function(data) {if(data=="успешно"){location.reload();}else{alert(data);}}',

                        )
                    )
                )
            )
        ),


    ),
));
echo CHtml::link('добавить роль', Yii::app()->createUrl('/back/membership/createRole/'));

?>
<div id="rlU"></div>