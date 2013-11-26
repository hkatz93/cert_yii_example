<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			/* uncomment the following to provide test database connection
			'db'=>array(
				'connectionString'=>'DSN for test database',
			),*/
                    	'db'=>array(
                            'class' =>'CDbConnection',
                            'connectionString' => 'mysql:host=localhost;dbname=certifications_test',
                            'emulatePrepare' => true,
                            'username' => 'test_user',
                            'password' => 'test_user',
                            'charset' => 'utf8',
                        ),
			
                    'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, info, trace',
				),
                            ),
                        ),
		),
	)
);
