<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN_URL_TO_STATIC; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN_URL_TO_STATIC; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN_URL_TO_STATIC; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN_URL_TO_STATIC; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN_URL_TO_STATIC; ?>/css/form.css" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<header>
    <?php echo CHtml::encode(Yii::app()->name); ?>
</header><!-- header -->

<div class="container" id="page">

	<div id="mainmenu">
		<?php

        $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Поиск', 'url'=>array('/front/site/index')),
				array('label'=>'Контакты', 'url'=>array('/front/site/page', 'view'=>'about')),
//				array('label'=>'Contact', 'url'=>array('/front/site/contact')),
				array('label'=>'Вход', 'url'=>array('/front/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Выход ('.Yii::app()->user->name.')', 'url'=>array('/front/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Тест', 'url'=>array('/test/index'),'visible'=> Yii::app()->user->checkAccess("admin")),
                Yii::app()->user->checkAccess('boAdmin') ? array('label'=>'Админка', 'url'=>array('/back/')):'',
			),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

    <div id="content">
        <?php echo $content; ?>
    </div><!-- content -->

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; 2014-<?php echo date('Y'); ?> by Sluchaj from Baily.<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
