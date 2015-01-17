<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../../../usr/share/php5/yii/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($config);
function shutdown(){
    Yii::app()->end();
}
register_shutdown_function('shutdown');