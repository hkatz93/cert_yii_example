<?php
$this->breadcrumbs=array(
	'Applications'=>array('index'),
	$model->app_id,
);

$this->menu=array(
	//array('label'=>'List Applications', 'url'=>array('index')),
	//array('label'=>'Create CertificationApplications', 'url'=>array('create')),
	array('label'=>'Update Application', 'url'=>array('update', 'id'=>$model->app_id)),
	array('label'=>'Delete Application', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->app_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Applications', 'url'=>array('admin')),
);
?>


<h2>Application #<?php echo $model->app_id; echo " (".Products::productId2Cid($model->product_id).")"; ?></h2>

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

$status_list = $model->statusList();
$type_list = $model->typeOptionList();
?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'app_id',
                array(
                    'label'=>'Product ID',
                    'type'=>'raw',
                    'value'=>CHtml::link($model->product_id,
                                 array('products/view','id'=>$model->product_id)),
            ),
            
                array(
                    'label'=>'Certification Type',
                    'type'=>'raw',
                    'value'=> $type_list[$model->certification_type],
                 ),
		//'requested_by',
            //'users.username',
                array(
                    'label'=>'Requested By',
                    'type'=>'raw',
                    'value'=>CHtml::encode($model->users->username).' '. CHtml::link('[View]',
                                 array('users/view','id'=>$model->users->user_id)),
                ),

		'company_contact',
		'date_submitted',
		//'certifying_lab',
            //'labs.company_name', // works but we need to change label
                
            /* working but no existing view for labs yet
            array(
                'label'=>'Certifying Lab',
                'type'=>'raw',
                'value'=>CHtml::encode($model->labs->company_name).' '. CHtml::link('[View]',
                                 array('labs/view','id'=>$model->labs->lab_id)),
            ),
             *
             */
		
		
		
                array(
                    'label'=>'Status',
                    'type'=>'raw',
                    'value'=> $status_list[$model->status],
                 ),
                'date_finalized_results',
		'date_staff_reviewed',
		'date_certified',
		
                array(
                    'label'=>'Staff Notes',
                    'type'=>'raw',
                    'value'=> $model->staff_notes,
                 ),
		
                array(
                    'label'=>'Committee Notes',
                    'type'=>'raw',
                    'value'=> $model->committee_notes,
                 ),
		
                array(
                    'label'=>'Lab Notes',
                    'type'=>'raw',
                    'value'=> $model->lab_notes,
                 ),
		'pending_firmware',
		'hold',
		
		
		'differences',
		'dependent_configuration',
		'module_changes',
		//'auto_delete',
		'test_plan',
                array(
                    'label'=>'Test Engine?',
                    'value' =>($model->test_engine) ?'Yes':'No'
                ),
            array(
                    'label'=>'Frequency Band Mode',
                    'type'=>'raw',
                    'value' =>($model->frequency_band_mode =='' || $model->frequency_band_mode =='NULL') ? 'single' : $model->frequency_band_mode,
                ),
                array(
                    'label'=>'Certified Spacial Streams',
                    'type'=>'raw',
                    'value' =>$certified_stream_table,
                ),
                array(
                    'label'=>'Agree Single Stream',
                    'value' =>($model->agree_single_stream == 1) ? 'Yes':'No',
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
                    'value'=> $model->labs->company_name,
                ),
                'date_lab_accepted',
		
	),
)); ?>
