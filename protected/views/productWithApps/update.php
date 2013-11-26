<?php
$this->breadcrumbs=array(
	'Products'=>array('index'),
	$product->product_id=>array('view','id'=>$product->product_id),
	'Update',
);

$this->menu=array(
	//array('label'=>'List Products', 'url'=>array('index')),
	//array('label'=>'Create Products', 'url'=>array('create')),
	array('label'=>'View Product', 'url'=>array('view', 'id'=>$product->product_id)),
	//array('label'=>'Manage Products', 'url'=>array('admin')),
        array('label'=>'Manage Products', 'url'=>Yii::app()->createUrl("products/admin")),
);
?>

<h2>Update Product <?php /*echo $model->cid;*/ ?></h2>

<?php echo $this->renderPartial('_form', array('product'=>$product, 'apps'=>$apps)); ?>