<?php $this->beginContent(); ?>

<div id="backmenu">
		<?php

        $this->widget('zii.widgets.CMenu',array(
            'items'=>array(
                array('label'=>'Админка', 'url'=>array('back/')),
                array('label'=>'cms', 'url'=>array('back/cms/index')),
            ),
        )); ?>
</div>

        <?php echo $content; ?>


<?php $this->endContent(); ?>