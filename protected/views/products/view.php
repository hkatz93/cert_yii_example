<?php
$this->breadcrumbs=array(
	'Products'=>array('index'),
	$model->product_id,
);

$this->menu=array(
	//array('label'=>'List Products', 'url'=>array('index')),
	//array('label'=>'Create Products', 'url'=>array('create')),
	array('label'=>'Edit Product', 'url'=>array('update', 'id'=>$model->product_id)),
	array('label'=>'Delete Product', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->product_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Products', 'url'=>array('admin')),
);
?>

<h2> <?php echo $model->cid; ?></h2>

<?php

$cert_rows = $model->certificationsArray();
//print_r($cert_rows);
$cert_list = array();
foreach ($cert_rows as $r){
    if ($r['cert_id'] == Certifications::CERT_WPS_PIN){
        $cert_list[] = '<b>Wi-Fi Protected Setup&trade;1</b>';
    }
    if ($r['cert_id'] == Certifications::CERT_WPS2_PIN){
        $cert_list[] = '<b>Wi-Fi Protected Setup&trade;2</b>';
    }
    $cert_list[] = $r['display_name'];
}

$related_app_links = array();
foreach ($model->certification_applications as $ca){
    $related_app_links[] = CHtml::link($ca->app_id,
            array('certificationApplications/view','id'=>$ca->app_id));
}

$certified_stream_table = "

    <table cellpadding='0' cellspacing='1' border='0' style='width:auto'>
        <tr>
            <th>&nbsp;</th>
            <th style='text-align:left'><b>2.4 GHz</b></th>
            <th style='text-align:left'><b>5.0 GHz</b></th>
        </tr>
        <tr>
            <td><b>Transmit (Tx)</b></td>
            <td>{$model->certified_tx_spatial_stream_24}</td>
            <td>{$model->certified_tx_spatial_stream_50}</td>
        </tr>
        <tr>
            <td><b>Receive (Rx)</b></td>
            <td>{$model->certified_rx_spatial_stream_24}</td>
            <td>{$model->certified_rx_spatial_stream_50}</td>
        </tr>
    </table>
    ";

$supported_stream_table = "

    <table cellpadding='0' cellspacing='1' border='0' style='width:auto'>
        <tr>
            <th>&nbsp;</th>
            <th style='text-align:left'><b>2.4 GHz</b></th>
            <th style='text-align:left'><b>5.0 GHz</b></th>
        </tr>
        <tr>
            <td><b>Transmit (Tx)</b></td>
            <td>{$model->supported_tx_spatial_stream_24}</td>
            <td>{$model->supported_tx_spatial_stream_50}</td>
        </tr>
        <tr>
            <td><b>Receive (Rx)</b></td>
            <td>{$model->supported_rx_spatial_stream_24}</td>
            <td>{$model->supported_rx_spatial_stream_50}</td>
        </tr>
    </table>
    ";

?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
                'cid',
		'product_name',
                array(
                    'label'=>'Company',
                    'type'=>'raw',
                    'value'=>$model->companies->company_name,
                ),
		

		'model_number',
		'sku',
		'additional_skus',
		'firmware',
		'wireless_chipset',
                array(
                    'label'=>'Device Type',
                    'value'=>$model->device_types->name,
                ),
		
                array(
                    'label'=>'Primary Category',
                    'value'=>$model->product_categories->category,
                ),
		'category_other',
		
                array(
                    'label'=>'Is the product a Module?',
                    'value'=>($model->is_module)?'Yes':'No',
                ),
	      array(
                    'label'=>'Is the product a MRCL Re-certifiable?',
                    'value'=>($model->is_mrcl_recertifiable)?'Yes':'No',
                ),
		
                array(
                    'label'=>'Is the product an ASD?',
                    'value'=>($model->is_asd)?'Yes':'No',
                ),
		'asd_test_plan',
		
                array(
                    'label'=>'Is the product Dependent?',
                    'type'=>'raw',
                    'value'=>($model->is_dependent)
                            ? 'Yes - '. CHtml::link(Products::productId2Cid($model->parent_id),
                                        array('products/view','id'=>$model->parent_id))
                            : 'No',
                ),
                
                array(
                    'label'=>'Transfer Certification?',
                    'value'=>($model->transfer_source == 0)
                            ? 'No'
                            : 'Yes - '. CHtml::link(Products::productId2Cid($model->transfer_source),
                                    array('products/view','id'=>$model->transfer_source)),
                ),
                //'product_url',
                array(
                    'label'=>'Product URL',
                    'type'=>'raw',
                    'value'=>CHtml::link($model->product_url, $model->product_url),

                ),
                array(
                    'label'=>'Obsolete?',
                    'value'=>($model->obsolete)?'Yes':'No',
                ),
		
                array(
                    'label'=>'Product Notes',
                    'type'=>'raw',
                    'value'=>$model->product_notes,
                ),
		'publish_on',
		'deferred_date',
		
		
                 array(
                    'label'=>'Display Publicly?',
                    'value'=>($model->admin_override_display == 0)?'Yes':'No',
                ),
                array(
                    'label'=>'Product Certifications',
                    'type'=>'raw',
                    
                    'value' =>implode("<br />", $cert_list)
                ),
                'cloned_from',
                array(
                    'label'=>'Supported Spacial Streams',
                    'type'=>'raw',
                    'value' =>$supported_stream_table,
                ),
                array(
                    'label'=>'Certified Spacial Streams',
                    'type'=>'raw',
                    'value' =>$certified_stream_table,
                ),
		//'supported_tx_spatial_stream_24',
		//'supported_rx_spatial_stream_24',
		//'supported_tx_spatial_stream_50',
		//'supported_rx_spatial_stream_50',
		//'certified_tx_spatial_stream_24',
		//'certified_rx_spatial_stream_24',
		//'certified_tx_spatial_stream_50',
		//'certified_rx_spatial_stream_50',
		'external_registrar_support',
                array(
                    'label'=>'Internal Registrar Support',
                    'value'=>($model->internal_registrar_support == 1)?'Yes':'No',
                ),
                array(
                    'label'=>'Related Applications',
                    'type'=>'raw',
                    'value'=>implode(' ',$related_app_links),
                ),
                array(
                    'label'=>'Public Certificate',
                    'type'=>'raw',
                    'value'=>CHtml::link('view certificate', "/pdf_certificate.php?cid=".$model->cid, array('target'=>'_blank')),
                ),
                array(
                    'label'=>'Member Certificate',
                    'type'=>'raw',
                    'value'=>CHtml::link('view certificate', "/pdf_certificate_member.php?cid=".$model->cid, array('target'=>'_blank')),
                )
                
	),
)); ?>
