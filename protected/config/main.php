<?php
$envFile = dirname(__FILE__).'/env.php';
if(!file_exists($envFile)) die('configuration file error');
if(!file_exists(dirname(__FILE__).'/../../.htaccess')) die('htaccess error');
require_once $envFile;
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	//текучка
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'BAILY',
    'language'=>'ru',
	// грузим компонент для логирования
	'preload'=>array('log'),

	// подгружаем классы, которые нужны, желательно, без звездочек
	'import'=>array(
        'application.controllers.*',
        'application.controllers.bo.*',
        'application.models.*',
		'application.components.*',
        'application.models.AR.*',
        'application.models.AR.EntityAPI.*',
        'application.models.AR.AuthItem.*',
        'application.models.AR.Search.*',
        'application.models.CFormModel.*',
        'application.models.CFormModel.Upload.*',
        'application.components.repositories.*',
        'ext.imperavi-redactor-widget.ImperaviRedactorWidget',
        'application.models.ViewModels.*',
        'application.components.widgets.*',
        'application.components.widgets.MyWidget.*',
        'application.components.widgets.MyWidget.MyCheckBoxList.*'
	),

	'modules'=>array(
		// автоматическая генерация кода
        'inventory',
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'q 1234567',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1','192.168.1.121'),
		),

	),

	// компоненты приложения
	'components'=>array(
		'user'=>array(
			// включаем аутентификацию использующую кукисы
			'allowAutoLogin'=>true,
		),

        'authManager'=>array(
            'class'=>'CDbAuthManager',
            'connectionID'=>'db',
        ),


		// URL-manager разбирает URL соответствующего формата
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
            'urlSuffix'=>'/',
			'rules'=>array(
                //базовые правила
                '/'=>'front/site/index/',
                //правила для админки
                'back/'=>'/back/index/',
                'back/<controller:\w+>' =>'bo/<controller>B/index/',
                'back/<controller:\w+>/<action:\w+>'=>'bo/<controller>B/<action>/',
                'back/weather/<action:\w+>/<alias:.*>'=>'bo/weatherB/<action>/',//для понимания погодного индентификатора в get запросе
                'back/<controller:\w+>/<action:\w+>/<alias>'=>'bo/<controller>B/<action>/',

//                '<controller:\w+>/<action:\w+>/'=>'<controller>/<action>',
//				'front/<controller:\w+>/<id:\d+>'=>'front/<controller>/view',
//				'front/<controller:\w+>/<action:\w+>/<id:\d+>'=>'front/<controller>/<action>',
//				'front/<controller:\w+>/<action:\w+>'=>'front/<controller>/<action>',


			),
		),
        'dm'=>array(
            'class'=>'DataManager',
        ),
		'db'=>array(
			'connectionString' => 'mysql:host='.DB_HOST.';dbname='.DB_NAME,
            'emulatePrepare'=>true,
            'username' =>DB_USERNAME,
            'password'=>DB_PASSWORD,
            'charset'=>'utf8'
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'front/site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, info',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
        'cache'=>array(
            'class'=>'system.caching.CMemCache',
            'serializer'=>array(
                'igbinary_serialize',
                function($v){
                    return @igbinary_unserialize($v);
                }
            ),
            'servers'=>array(
                array(
                  'host'=>'localhost',
                  'port'=>11211,
                ),
            ),
        ),

	),

	'params'=>array(
		// это, возможно, где-то будет использоваться
		'adminEmail'=>'ranvas@baily.ru',
        'noreplyEmail'=>'ranvas@baily.ru',
	),
);