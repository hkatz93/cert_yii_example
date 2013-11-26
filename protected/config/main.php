<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

if (isset($_SERVER['DEPLOYMENT_TYPE']) && !empty($_SERVER['DEPLOYMENT_TYPE'])){
    // assign server specific settings
    switch($_SERVER["DEPLOYMENT_TYPE"]) {
        case 'DEV':     $db_passwd = 'Y4r1KYp7tz63xgQ'; break; // dev
        case 'TEST':    $db_passwd = 'ySuoq@Sr1dhtLCO'; break; // staging
        default:        $db_passwd = 'fQo30XwVa95ntdV'; break; // production (DEFINED IN 2 PLACES! see below)

    }
}
else {
    $db_passwd = 'fQo30XwVa95ntdV'; // production (DEFINED IN 2 PLACES! see above)
}

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Certifications Super-Administration',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		//'gii'=>array(
		//	'class'=>'system.gii.GiiModule',
		//	'password'=>'giipass',
                  //     'ipFilters'=>array('192.168.0.122', '192.168.0.123','192.168.0.101'),
                  //     'newFileMode'=>0666,
                  //       'newDirMode'=>0777,

		//),
		
	),

	// application components
	'components'=>array(
		
            'user'=>array(
                        //'class' => 'CWebUser',
			// enable cookie-based authentication
			//'allowAutoLogin'=>false,
                        'allowAutoLogin'=>true,
		),
          
         
		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
		//'db'=>array(
		//	'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		//),
		// uncomment the following to use a MySQL database
	
		'db'=>array(
			//'connectionString' => 'mysql:host=localhost;dbname=certifications',
                        'class' => 'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=certifications',
			'emulatePrepare' => true,
			'username' => 'wifi_admin', // switch to wifi when not using gii
			'password' => $db_passwd,
			'charset' => 'utf8',
			//'enableParamLogging'=>true, /* turn off for production */
			//'enableProfiling'=>true, /* turn off for production */
		),
                
		'errorHandler'=>array(
		  // use 'site/error' action to display errors
                  'errorAction'=>'site/error',
                ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, info, trace',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
                // location of uploaded data
                'uploaded_data_dir' => realpath(dirname(__FILE__).'/../../../files/uploaded_test_data/'),
                'uploaded_data_webpath' => '/files/uploaded_test_data/',
		// this is used in contact page
		'adminEmail'=>'hkatz93@gmail.com',
		'adminEmail'=>'webmaster@example.com',
                'default_admin_username' => 'hkatz@wi-fi.org', // from users table

		'STATUS_STEP1' => 1,
		'STATUS_STEP2' => 2,
		'STATUS_STEP3' => 3,
		'STATUS_STEP4' => 4,
		'STATUS_STEP5' => 5,
		'STATUS_STEP6' => 6,
		'STATUS_COMPLETE' => 7,
		'STATUS_HOLD'  => 23,

		'CERT_TYPE_NEW' => 9,
		'CERT_TYPE_ADDITIONAL' => 10,
		'CERT_TYPE_RECERT' => 11,
		'CERT_TYPE_DEPENDENT' => 12,
		'CERT_TYPE_TRANSFER' => 22,

		'RESULT_PASS' => 13,
		'RESULT_FAIL' => 14,
		'RESULT_NOT_TESTED' => 21,

		'ACCESS_LEVEL_ADMIN' => 15,
		'ACCESS_LEVEL_USER' => 16,
		'ACCESS_LEVEL_SUPER' => 50,

		'STATUS_LIVE'  => 17,
		'STATUS_DELETE_ME' => 18,
		'STATUS_FAILED' => 19,
		'STATUS_INACTIVE' => 20


	),
);
