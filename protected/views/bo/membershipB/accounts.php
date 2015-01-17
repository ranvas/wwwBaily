<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$accounts->search(),
    'columns'=>array(
        'id',
        'username',
        'password',
        array(
            'name'=>'role',
            'type'=>'raw',
            'value'=>'CHtml::dropDownList("Account[role][]",$data->role, $data->roles ,array(
                "ajax"=>array(
                    "data"=>array(
                        "id"=>$data->id,
                        "role"=>"js:this.value",
                    ),
                    "type"=>"POST",
                    "url"=>Yii::app()->createUrl("/back/membership/changeAccount/"),
                    "update"=>"#ajax"
                ),
                "confirm"=>"Are you sure?",
                "id"=>"acc_rl_".$data->id))',
        ),
        array(
            'name'=>'status',
            'type'=>'raw',
            'value'=>'CHtml::dropDownList("Account[status][]",$data->status, array("0" => "banned", "1" => "active"),array(
                "ajax"=>array(
                    "data"=>array(
                        "id"=>$data->id,
                        "status"=>"js:this.value",
                    ),
                    "type"=>"POST",
                    "url"=>Yii::app()->createUrl("/back/membership/changeAccount/"),
                    "update"=>"#ajax"
                ),
                "confirm"=>"Ты уверен?",
                "id"=>"acc_st_".$data->id))',
        ),
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'buttons'=>array
            (
                'update'=>array(
                    'url'=>'Yii::app()->createUrl("/back/membership/changeAccount/".$data->id)',
                )
            )

        )

    )
));

?>
    <div id="ajax"></div>
<?php echo CHtml::link('добавить пользователя', Yii::app()->createUrl('/back/membership/createUser/')).' | ';?>
<?php echo CHtml::link('добавить администратора', Yii::app()->createUrl('/back/membership/createAdmin/')).' | ';?>
<?php echo CHtml::link('добавить менеджера', Yii::app()->createUrl('/back/membership/createManager/'));?>
