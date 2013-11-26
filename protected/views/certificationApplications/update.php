<?php
$this->breadcrumbs=array(
	'Applications'=>array('index'),
	$model->app_id=>array('view','id'=>$model->app_id),
	'Update',
);

$this->menu=array(
	//array('label'=>'List Applications', 'url'=>array('index')),
	//array('label'=>'Create CertificationApplications', 'url'=>array('create')),
	array('label'=>'View Application', 'url'=>array('view', 'id'=>$model->app_id)),
	array('label'=>'Manage Applications', 'url'=>array('admin')),
);
?>

<h2>Update Application #<?php echo $model->app_id; ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>