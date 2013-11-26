<?php

/**
 * populates test database with data from fixutures
 * This is not needed for unit tests since this is done automatically,
 * but it is helpful to re-create the data for manual testing.
 * 
 *	Usages: <code>
 *	php populate_fixtures.php
 *	</code>
 */
require 'bootstrap.php';

$connection=Yii::app()->db;   // assuming you have configured a "db" connection

// If not, you may explicitly create a connection:
// $connection=new CDbConnection($dsn,$username,$password);
//$sql = "SHOW COLUMNS FROM :table";

// grab the fixtures filenames:

// open this directory
$dirname = dirname(__FILE__)."/fixtures/";
$myDirectory = opendir($dirname);

// get each file ending with .php
while($entryName = readdir($myDirectory)) {
    if (preg_match('/\.php$/', $entryName)){
	$dirArray[] = $entryName;
    }
}

// close directory
closedir($myDirectory);

print_r($dirArray);
$connection=Yii::app()->db;
//Yii::app()->
$i = 0;
foreach ($dirArray as $filename){
    // create the functions
    $table_name = str_replace('.php','',$filename);
    print "table_name = $table_name\n";
    $fixture_filename = $dirname . $filename;
    print "\$fixture_filename =$fixture_filename \n";
    $_getData = create_function('', "return require('$fixture_filename');");
        
    
    
    print "grabbing data for fixuture file $filename";
    $data = $_getData();

    //$rv = $connection->createCommand()->truncateTable($table_name)->execute();
    $command = $connection->createCommand();
    //print_r($command);
    $num_rows = $command->truncateTable($table_name);
    //print "cmd = ";print_r($cmd);

    foreach ($data as $row){
        $rv = $connection->createCommand()
            ->insert($table_name, $row);
        if ($rv == 0) {
            trigger_error("row was not inserted for table $table", E_USER_WARNING);
        }
            
    }
    
    
}

exit;

?>
