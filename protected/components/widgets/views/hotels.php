<?php
    echo CHtml::activeDropDownList($model,'hotel',$model->getHotels(),array('prompt'=>'выбрать отель'));
?>
