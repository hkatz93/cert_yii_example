<?php
$this->breadcrumbs=array(
	'Product With Apps'=>array('/productWithApps'),
	'View',
);?>
<?php
//$this->breadcrumbs=array(
//	'Products'=>array('index'),
//	$model->product_id,
//);

$this->menu=array(
	//array('label'=>'List Products', 'url'=>array('index')),
	//array('label'=>'Create Products', 'url'=>array('create')),
	array('label'=>'Edit Product', 'url'=>array('update', 'id'=>$model->product_id)),
	array('label'=>'Delete Product', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->product_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Products', 'url'=>Yii::app()->createUrl("products/admin")),
);


$dep_prods = $model->getDependentProducts();
foreach ($dep_prods as $dep_prod){
              //echo ' '.$dep_prod->cid;
              $dep_prod_list[] = CHtml::link($dep_prod->cid, array('productWithApps/view','id'=>$dep_prod->product_id));
}
if (is_array($dep_prod_list)){
    $dep_prod_list_html = implode(' ',$dep_prod_list);
}
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
	<tr class='cert_ac_class'>
		  <td>&nbsp;</td>
		  <th colspan='2' style='text-align: center'><b>802.11n</b></th>
		  <th style='text-align: center'>&nbsp;<b>802.11ac</b>&nbsp;</th>
	</tr>
        <tr>
            <th>&nbsp;</th>
            <th style='text-align:left'><b>2.4 GHz</b></th>
            <th style='text-align:left'><b>5.0 GHz</b></th>
            <th style='text-align:left' class='cert_ac_class'><b>5.0 GHz</b></th>
        </tr>
        <tr>
            <td><b>Transmit (Tx)</b></td>
            <td>{$model->certified_tx_spatial_stream_24}</td>
            <td>{$model->certified_tx_spatial_stream_50}</td>
            <td class='cert_ac_class'>{$model->certified_tx_spatial_stream_50_ac}</td>
        </tr>
        <tr>
            <td><b>Receive (Rx)</b></td>
            <td>{$model->certified_rx_spatial_stream_24}</td>
            <td>{$model->certified_rx_spatial_stream_50}</td>
            <td class='cert_ac_class'>{$model->certified_rx_spatial_stream_50_ac}</td>
        </tr>
    </table>
    ";

$supported_stream_table = "

    <table cellpadding='0' cellspacing='1' border='0' style='width:auto'>
	<tr class='cert_ac_class'>
		  <td>&nbsp;</td>
		  <th colspan='2' style='text-align: center'><b>802.11n</b></th>
		  <th style='text-align: center'>&nbsp;<b>802.11ac</b>&nbsp;</th>
	</tr>
        <tr>
            <th>&nbsp;</th>
            <th style='text-align:left'><b>2.4 GHz</b></th>
            <th style='text-align:left'><b>5.0 GHz</b></th>
            <th style='text-align:left' class='cert_ac_class'><b>5.0 GHz</b></th>
        </tr>
        <tr>
            <td><b>Transmit (Tx)</b></td>
            <td>{$model->supported_tx_spatial_stream_24}</td>
            <td>{$model->supported_tx_spatial_stream_50}</td>
            <td class='cert_ac_class'>{$model->supported_tx_spatial_stream_50_ac}</td>
        </tr>
        <tr>
            <td><b>Receive (Rx)</b></td>
            <td>{$model->supported_rx_spatial_stream_24}</td>
            <td>{$model->supported_rx_spatial_stream_50}</td>
            <td class='cert_ac_class'>{$model->supported_rx_spatial_stream_50_ac}</td>
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
                    'value'=>$model->companies->company_name . (($model->companies->status == Companies::INACTIVE) ?'<span style="font-weight:bold; color:red"> (inactive)</span>': ''),
                ),


		'model_number',
		'sku',
		'additional_skus',
		       array(
                    'label'=>'Operating System',
                    'value'=>$model->os->name . (!empty($model->os_other) ? ': ' . $model->os_other : ''),
                ),
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
                    'type'=>'raw',
                    'value'=>($model->is_module) ? "Yes (dependent products: $dep_prod_list_html)" : 'No',
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
                                        array('productWithApps/view','id'=>$model->parent_id))
                            : 'No',
                ),

                array(
                    'label'=>'Transfer Certification?',
                    'value'=>($model->transfer_source == 0)
                            ? 'No'
                            : 'Yes - '. CHtml::link(Products::productId2Cid($model->transfer_source),
                                    array('productWithApps/view','id'=>$model->transfer_source)),
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
                    'value'=>($model->admin_override_display == 1)?'Yes':'No',
                ),
                array(
                    'label'=>'Product Certifications',
                    'type'=>'raw',

                    'value' =>implode("<br />", $cert_list)
                ),
                'cloned_from',
                array(
                    'label'=>'Supported Spatial Streams',
                    'type'=>'raw',
                    'value' =>$supported_stream_table,
                ),
                array(
                    'label'=>'Certified Spatial Streams',
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

<!-- now show apps -->
<?php

foreach ($apps as $app){
    echo "<hr/>";
    //<h2>Application #<?php echo $app->app_id; echo " (".Products::productId2Cid($model->product_id).")"; </h2>

    $cert_rows = $app->certificationsArray();
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

    $certified_stream_table = "

        <table cellpadding='0' cellspacing='1' border='0' style='width:auto'>
	<tr class='cert_ac_class'>
		  <td>&nbsp;</td>
		  <th colspan='2' style='text-align: center'><b>802.11n</b></th>
		  <th style='text-align: center'>&nbsp;<b>802.11ac</b>&nbsp;</th>
	</tr>
            <tr>
                <th>&nbsp;</th>
                <th style='text-align:left'><b>2.4 GHz</b></th>
                <th style='text-align:left'><b>5.0 GHz</b></th>
                <th style='text-align:left' class='cert_ac_class'><b>5.0 GHz</b></th>
            </tr>
            <tr>
                <td><b>Transmit (Tx)</b></td>
                <td>{$app->certified_tx_spatial_stream_24}</td>
                <td>{$app->certified_tx_spatial_stream_50}</td>
                <td class='cert_ac_class'>{$app->certified_tx_spatial_stream_50_ac}</td>
            </tr>
            <tr>
                <td><b>Receive (Rx)</b></td>
                <td>{$app->certified_rx_spatial_stream_24}</td>
                <td>{$app->certified_rx_spatial_stream_50}</td>
                <td class='cert_ac_class'>{$app->certified_rx_spatial_stream_50_ac}</td>
            </tr>
        </table>
        ";

    $status_list = $app->statusList();
    $type_list = $app->typeOptionList();
    
    //$recert_type_ary = array( 'label'=>'Re-certification Type');
    if ($app->certification_type == CertificationApplications::TYPE_RECERT) {
		$recert_type_id_list = $app->recertTypeIdOptionList();
		$recert_type_ary = array();
		$recert_type_ary[] =
		array(
                        'label'=>'Re-certification Type',
                        'type'=>'raw',
                        'value'=> $recert_type_id_list[$app->recert_type_id] 
				. (($app->recert_type_id == CertificationApplications::RECERT_TYPE_ID_FIRMWARE_COSMETIC) 
					? "<br> <span style='font-style: italic'> [Product has not been re-tested. Results are from previous certifications.]</span>" : ''),
                     );
		$recert_type_ary[] =
		array(
                        'label'=>'Re-certification Notes',
                        'type'=>'raw',
                        'value'=> $model->recert_notes,
                     );
	}

	$output_ary = array(
            'data'=>$app,
            'attributes'=>array(
                    'app_id',
                    array(
                        'label'=>'Product ID',
                        'type'=>'raw',
                        'value'=>CHtml::link($app->product_id,
                                     array('products/view','id'=>$app->product_id)),
                ),

                    array(
                        'label'=>'Certification Type',
                        'type'=>'raw',
                        'value'=> $type_list[$app->certification_type],
                     ),
	));
	
	if (!empty($recert_type_ary)) {
		foreach ($recert_type_ary as $r_ary){
			$output_ary['attributes'][] = $r_ary;
		}
		
	}
	$push_attr_ary = array(
		
                    //'requested_by',
                //'users.username',
                    array(
                        'label'=>'Requested By',
                        'type'=>'raw',
                        'value'=>CHtml::encode($app->users->username).' '. CHtml::link('[View]',
                                     array('users/view','id'=>$app->users->user_id)),
                    ),
                    'company_contact',
                    'date_submitted',
                    array(
                        'label'=>'Status',
                        'type'=>'raw',
                        'value'=> $status_list[$app->status],
                     ),
                    'date_finalized_results',
                    'date_staff_reviewed',
                    'date_certified',

                    array(
                        'label'=>'Staff Notes',
                        'type'=>'raw',
                        'value'=> $app->staff_notes,
                     ),

                    array(
                        'label'=>'Committee Notes',
                        'type'=>'raw',
                        'value'=> $app->committee_notes,
                     ),

                    array(
                        'label'=>'Lab Notes',
                        'type'=>'raw',
                        'value'=> $app->lab_notes,
                     ),
			  array(
                        'label'=>'Pending OS',
                        'type'=>'raw',
                        'value'=> $app->pending_os->name . (($app->pending_os->name == 'Other') ? ': ' . $app->pending_os_other : ''),
                     ),
			array(
                        'label'=>'Initial OS',
                        'type'=>'raw',
                        'value'=> $app->initial_os->name . (($app->initial_os->name == 'Other') ? ': ' . $app->initial_os_other : ''),
                     ),
		'pending_firmware',
		'initial_firmware',
		'pending_wireless_chipset',
		'initial_wireless_chipset',
                    'hold',


                    'differences',
                    'dependent_configuration',
                    'module_changes',
                    //'auto_delete',
                    'test_plan',
                    array(
                        'label'=>'Test Engine?',
                        'value' =>($app->test_engine) ?'Yes':'No'
                    ),
                array(
                        'label'=>'Frequency Band Mode',
                        'type'=>'raw',
                        'value' =>($app->frequency_band_mode =='' || $app->frequency_band_mode =='NULL') ? 'single' : $app->frequency_band_mode,
                    ),
                    array(
                        'label'=>'Certified Spatial Streams',
                        'type'=>'raw',
                        'value' =>$certified_stream_table,
                    ),
                    array(
                        'label'=>'Agree Single Stream',
                        'value' =>($app->agree_single_stream == 1) ? 'Yes':'No',
                    ),
                    array(
                        'label'=>'Certifications',
                        'type'=>'raw',
                        'value' =>implode("<br />", $cert_list)
                    ),
                    'publish_on',
                    'deferred_date',
                array(
                        'label'=>'Certifying Lab',
                        'type'=>'raw',
                        'value'=> $app->labs->company_name,
                    ),
                    'date_lab_accepted',

            );
	foreach ($push_attr_ary as $attr){
		$output_ary['attributes'][] = $attr;
	}
    
	
    $this->widget('zii.widgets.CDetailView', $output_ary);
}
?>

