<?php $this->beginContent('/layouts/back'); ?>

    <div id="cmsmenu">
        <?php
        //формируем массив для меню
        $items = array();
        if(Yii::app()->user->checkAccess('boAccounts'))
        {
            $items[] = array('label'=>'membership', 'url'=>array('back/membership/'));
        }
        if(Yii::app()->user->checkAccess('boSQL'))
        {
            $items[] = array('label'=>'sql', 'url'=>array('back/sql/'));
        }
        if(Yii::app()->user->checkAccess('boWeather'))
        {
            $items[] = array('label'=>'погода', 'url'=>array('back/weather/'));
        }
        if(Yii::app()->user->checkAccess('boAPI'))
        {
            $items[] = array('label'=>'API', 'url'=>array('back/api/'));
        }
        if(Yii::app()->user->checkAccess('boFilters'))
        {
            $items[] = array('label'=>'фильтры', 'url'=>array('back/filter/'));
        }



        $this->widget('zii.widgets.CMenu',array(
            'items'=>$items
        )); ?>
    </div>

<?php echo $content; ?>


<?php $this->endContent(); ?>