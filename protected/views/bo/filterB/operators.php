<?php
echo 'Операторы:';
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$model->operatorsDataProvider,
    'columns'=>array(
        'name',
        //привязанные страны
        array(
            'name'=>'countries',
            'type'=>'raw',
            'value'=>function($data) use($model)
            {
                $countries = $model->getCountriesByOperatorId($data->entityId);
                if($countries)
                {
                    return implode(",",functions::compactList($countries,'entityId','nameCyr'));
                }
                else
                {
                    return "страны не прикреплены";
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
                    //в гете передается operatorId
                    'url'=>'Yii::app()->createUrl("/back/filter/unbindOperator/".$data->entityId)',
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