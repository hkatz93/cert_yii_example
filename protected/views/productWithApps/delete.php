<?php
$this->breadcrumbs=array(
	'Product With Apps'=>array('/productWithApps'),
	'Delete',
);?>
<h1>Deleting Product <?php echo $product->cid; ?><!-- <?php echo $this->id . '/' . $this->action->id; ?>--></h1>

<p style='font-weight: bold;'><?php echo $message; ?></p>
<p>
    <?php
    foreach ($errors as $msg){
    ?>
    
        <p style='font-weight: bold; color:red;'><?php echo "$msg"; ?></p>
    <?php
    }
    ?>
<?php 
	foreach ($error_ary as $key =>$msgary) {
		foreach ($msgary as $msg) {
		?>
			<p><?php echo $msg; ?></p>
		<?php
		}
	}
?>
<!--<?php print_r($error_ary); ?>-->