<?php

// change the following paths if necessary
$local_yiit = dirname(__FILE__).'/../../../../yii/framework/yiit.php'; 
if (file_exists($local_yiit)){
	$yiit= $local_yiit;
}
else {
 	// change the following paths if necessary
	$yiit= '/usr/local/lib/php/yii/framework/yiit.php';
}
//$yiit='/usr/local/lib/php/yii/framework/yiit.php';

// load 2nd location from ini file
//$yiit_2= dirname(__FILE__).'/../../../../yii/framework/yiit.php';
$ini_filename = dirname(__FILE__).'/bootstrap.ini';
if (file_exists($ini_filename)) {
    $ini_ary = parse_ini_file($ini_filename);
    $yiit = $ini_ary['yiit'];
}
//print "yiit = $yiit\n";
//$yiit = (file_exists($yiit_1)) ? $yiit_1 : '';
//$yiit = (empty($yiit_1) && file_exists($yiit_2)) ? $yiit_2 : $yiit_1;
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($config);
