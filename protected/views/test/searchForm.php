<?php
return array(
    'title'=>'поисковая форма',
//    'action'=>Yii::app()->createUrl('test/index'),
//    'method'=>'get',
    'elements'=>array(
        'countryKey'=>array(
            'label'=>'страны',
            'type'=>'dropdownlist',
            'items'=>$this->model->countries,
            'value'=>$this->model->cacheSearch->countryKey,
            'ajax'=>array('type'=>'POST', 'url'=>Yii::app()->createUrl('test/countryChange'),'replace'=>'#yw0'),
        ),
//        'bDate1' => array(
//            'type' => 'application.components.widgets.CJuiDatePicker',
//            'options'=>array(
//                'dateFormat'=>'dd.mm.yy',
//                //'showAnim'=>'fold',
//            ),
//            'htmlOptions' => array(
//                'style'=>'height:20px;'
//            )
//        ),

    ),

    'buttons'=>array(
        'submit'=>array(
            'type'=>'submit',
            'label'=>'поиск',
        ),
    ),
);

