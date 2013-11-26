<?php

/**
 * generates PHPunit fixtures from tables
 * CAVEAT: rows are limited to 1000 to prevent accidentally hitting a table with
 * many 1000's of rows
* 
*	Usages: <code>
*	php table2fixture.php requested_certifications > ./fixtures/requested_certifications
*	</code>
 */
require 'bootstrap.php';

$connection=Yii::app()->db;   // assuming you have configured a "db" connection
// If not, you may explicitly create a connection:
// $connection=new CDbConnection($dsn,$username,$password);
//$sql = "SHOW COLUMNS FROM :table";

$table = $argv[1];
$valid_tables[]='certification_applications';
$valid_tables[]='test_data';
$valid_tables[]='test_results';
$valid_tables[]='requested_certifications';
$valid_tables[]='product_certifications';
$valid_tables[]='products';
print "<"."?php\n\n return ";
if (in_array($table, $valid_tables)){
    /*
    $sql = "SELECT * FROM $table LIMIT 1000";    
    $command=$connection->createCommand($sql);
    $dataReader=$command->query();
    $rows=$dataReader->readAll();
     *
     */
    //$dr =$connection->createCommand()->select('*')->from($table)->limit(1000)->query();
    //$rows = $dr->readAll();
    $rows =$connection->createCommand()->select('*')->from($table)->limit(1000)->query()->readAll();
    var_export($rows);
    //$affected_rows =$connection->createCommand()->truncateTable($table);
    //$rows =$connection->createCommand()->select('*')->from($table)->limit(1000)->query()->readAll();
    //var_export($rows);
}
else {
    throw new Exception("valid tables are:".(implode(',',$valid_tables)));
}
print ";"


?>
