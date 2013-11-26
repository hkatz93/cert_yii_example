<?php
$this->breadcrumbs=array(
	'Applications',
);

$this->menu=array(
	//array('label'=>'Create CertificationApplications', 'url'=>array('create')),
	array('label'=>'Manage Applications', 'url'=>array('admin')),
);
?>

<h1> Applications</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
