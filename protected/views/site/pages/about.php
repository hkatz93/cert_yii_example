<?php
$this->pageTitle=Yii::app()->name . ' - About';
$this->breadcrumbs=array(
	'About',
);
?>
<h1>About</h1>

<p>This is a "static" page. You may change the content of this page
by updating the file <tt><?php echo __FILE__; ?></tt>.</p>

<p>Attempting a printout of certifications</p>
<?php


//$certs = Certifications::model()->findAll(); // this will lazy load categories
$certs = Certifications::model()->with('categories')->findAll(); // this will eager load categories
$arr = array();
$arr2 = array();
foreach($certs as $c)
{
    //$arr[$c->cert_id] = $c->attributes;
	$arr2[$c->cert_id] = $c->categories->class;
	print "cert_id = {$c->cert_id}, display_name {$c->display_name}, <br>\n";
}
?>
<pre>
<?php 
	//print_r($arr); 
?>
<hr>
<?php 
	print_r($arr2); 
?>


<?php
	//$certs = Certifications::model()->findAll();

?>
</pre>
