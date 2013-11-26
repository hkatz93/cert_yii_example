<?php
$this->breadcrumbs=array(
	'Applications'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Applications', 'url'=>array('index')),
	array('label'=>'Manage Applications', 'url'=>array('admin')),
);
?>

<h1>Create Applications</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>