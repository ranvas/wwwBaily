<?php


return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
        'preload'=>array('log'),
        'import'=>array(
            'application.controllers.*',
            'application.models.*',
            'application.components.*',
            'application.models.AR.*',
            'application.models.AR.EntityAPI.*',
            'application.models.AR.Search.*',
            'application.models.CFormModel.*',
            'application.models.CFormModel.Upload.*',
            'application.components.repositories.*',
            'ext.imperavi-redactor-widget.ImperaviRedactorWidget',
        ),
		'components'=>array(
            'user'=>array(
                // включаем аутентификацию использующую кукисы
                'allowAutoLogin'=>true,
            ),
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
            'authManager'=>array(
                'class'=>'CDbAuthManager',
                'connectionID'=>'db',
            ),
            'BailyAPI'=>array(
                'class'=>'application.components.BailyAPI'
            ),

            'db'=>array(
                'connectionString' => 'mysql:host='.DB_HOST.';dbname=test',
                'emulatePrepare'=>true,
                'username' =>DB_USERNAME,
                'password'=>DB_PASSWORD,
                'charset'=>'utf8'
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
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'CFileLogRoute',
                        'levels'=>'error, warning, info',
                    ),
                ),
            ),
		),

	)
);
