<?php
$this->breadcrumbs=array(
	'Products'=>array('index'),
	'Manage',
);

$this->menu=array(
	//array('label'=>'List Products', 'url'=>array('index')),
	//array('label'=>'Create Products', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('products-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2>Manage Products</h2>

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
	'id'=>'products-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
                'cid',
		//'product_id',
		//'company_id',
		'product_name',
		'model_number',
		//'sku',
		//'additional_skus',
		/*
		'firmware',
		'wireless_chipset',
		'type_id',
		'category_id',
		'category_other',
		'is_module',
		'is_mrcl_recertifiable',
		'is_asd',
		'asd_test_plan',
		'is_dependent',
		'product_url',
		'transfer_source',
		'parent_id',
		'cid',
		'obsolete',
		'product_notes',
		'publish_on',
		'deferred_date',
		'cloned_from',
		'admin_override_display',
		'supported_tx_spatial_stream_24',
		'supported_rx_spatial_stream_24',
		'supported_tx_spatial_stream_50',
		'supported_rx_spatial_stream_50',
		'certified_tx_spatial_stream_24',
		'certified_rx_spatial_stream_24',
		'certified_tx_spatial_stream_50',
		'certified_rx_spatial_stream_50',
		'external_registrar_support',
		'internal_registrar_support',
		*/
		array(
			'class'=>'CButtonColumn',
                    'deleteConfirmation'=>"js:'WARNING! Product with CID '+$(this).parent().parent().children(':first-child').text()+' will be deleted! This removes all traces of this product and its data. Continue?'",
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
