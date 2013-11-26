<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
//return array(
//	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
//	'name'=>'My Console Application',
//);

$_SERVER['DEPLOYMENT_TYPE'] = (!empty($_ENV['DEPLOYMENT_TYPE'])) ? $_ENV['DEPLOYMENT_TYPE'] : '';

function testConfig(){
    require(dirname(__FILE__).'/test.php');
}
function getConfig(){
    $ary = testConfig();
    // remove user component
    unset($ary['components']['user']);
    return $ary;
}
return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'), // uncomment either main.php or test.php not both
        //require(dirname(__FILE__).'/test.php'),
        //getConfig(),
        array(
            'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
            'name'=>'My Console Application',
            
            )
     );