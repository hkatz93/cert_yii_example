<?php
$this->breadcrumbs=array(
	'Applications'=>array('index'),
	'Manage',
);

$this->menu=array(
	//array('label'=>'List Applications', 'url'=>array('index')),
	//array('label'=>'Create CertificationApplications', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('certification-applications-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2>Manage Applications</h2>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'certification-applications-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'app_id',
		'product_id',
		//'certification_type',
		//'requested_by',
		//'company_contact',
		'date_submitted',
//		'requested_certifications',
//		array(
//			'name' =>'requested_certifications',
//			'type' => 'raw',
//			'value' => 1),
		/*
		'certifying_lab',
		'date_lab_accepted',
		'date_finalized_results',
		'date_staff_reviewed',
		'date_certified',
		'status',
		'staff_notes',
		'committee_notes',
		'lab_notes',
		'hold',
		'publish_on',
		'deferred_date',
		'differences',
		'dependent_configuration',
		'module_changes',
		'auto_delete',
		'test_plan',
		'test_engine',
		'frequency_band_mode',
		'certified_tx_spatial_stream_24',
		'certified_rx_spatial_stream_24',
		'certified_tx_spatial_stream_50',
		'certified_rx_spatial_stream_50',
		'agree_single_stream',
		*/
		array(
			'class'=>'CButtonColumn',
                     'deleteConfirmation'=>"js:'WARNING! Application with ID '+$(this).parent().parent().children(':first-child').text()+' will be deleted! This removes all traces of this product and its data. Continue?'",
                    'template'=>'{view}{update}{delete}',
                    'buttons'=>array
                    (
                        'view'=>array
                        (
                            'url'=>'Yii::app()->createUrl("productWithApps/view", array("id"=>$data->product_id))',
                        ),
                        'update'=>array
                        (
                            'url'=>'Yii::app()->createUrl("productWithApps/update", array("id"=>$data->product_id))',
                        ),


                    )
		),
	),
)); ?>
