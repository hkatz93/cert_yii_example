<?php
$this->breadcrumbs=array(
	'Products'=>array('index'),
	$model->product_id=>array('view','id'=>$model->product_id),
	'Update',
);

$this->menu=array(
	//array('label'=>'List Products', 'url'=>array('index')),
	//array('label'=>'Create Products', 'url'=>array('create')),
	array('label'=>'View Product', 'url'=>array('view', 'id'=>$model->product_id)),
	array('label'=>'Manage Products', 'url'=>array('admin')),
);
?>

<h2>Update Product <?php /*echo $model->cid;*/ ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>