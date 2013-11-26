<?php
// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

$local_yii = dirname(__FILE__).'/../../../yii/framework/yiic.php'; 
if (file_exists($local_yii)){
	$yiic= '/usr/local/lib/php/yii/framework/yiic.php';
}
else {
 	// change the following paths if necessary
	$yiic= '/usr/local/lib/php/yii/framework/yiic.php';
}
$config=dirname(__FILE__).'/config/console.php';

require_once($yiic);
