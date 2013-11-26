<?php

$js = "
$('#user_name__$i').change(function() {
  if ($('#user_name__$i').val() == '') {
    $('#CertificationApplications_requested_by__$i').val('');
  }
});
";

Yii::app()->clientScript->registerScript("removeHiddenRequestByValue__$i", $js, CClientScript::POS_READY);
?>

<hr />
<style>
.product_cert_edit_table  {
	background-color: #D2EAFB;
}
.app_cert_edit_table  {
	background-color: #FF9;
}
.product_cert_edit td.category_column {
        border-style: solid none none none;
        border-width: 1px;
        border-color: blue;
}
.app_cert_edit td.category_column {
        border-style: solid none solid none;
        border-width: 1px;
        border-color: greenyellow;
}
.data_cell {
    background-color: white;
}
 </style>
<table border="1" class="app_cert_edit_table">
<tr>
  <td width="74"><span class="row"><?php echo CHtml::activeLabelEx($app, 'app_id'); ?></span></td>
  <td width="252"><span class="row"><?php echo $app->app_id; ?><?php echo CHtml::hiddenField("CertificationApplications[$i][app_id]", $app->app_id); ?>
      <?php
      //$dep_prods = $product->getDependentProducts();
      $dep_prod_id_list = $app->getImportReadyDependentProductIds();
      $already_imported_prod_id_list = $app->getImportedAppProductIds();
      // only allow imports for ADDITIONAL and RE-CERTIFICATION applications
      
      if ( ((count($dep_prod_id_list) > 0) || (count($already_imported_prod_id_list ) > 0))
	&& in_array($app->certification_type, 
			  array(CertificationApplications::TYPE_ADDITIONAL, CertificationApplications::TYPE_RECERT))) { 
         
         // foreach ($dep_prod_id_list as $dep_prod_id){
              //echo ' '.$dep_prod->cid;
           //   $dep_prod_list_str .= Products::productId2Cid($dep_prod_id) . " ";
			  
          //}
        //  $dep_prod_list_str = implode(',',$dep_prod_list);
		  
	echo ' Export into:';	  
	if ($app->status == CertificationApplications::STATUS_COMPLETE) {
		foreach ($dep_prod_id_list as $dep_prod_id) {
			$dep_prod_cid = Products::productId2Cid($dep_prod_id);
			echo CHtml::ajaxButton(
				"$dep_prod_cid",
				array('certificationApplications/ExportToDependentProduct'),
				array('type'=>'get', 'data'=>array('app_id'=>$app->app_id, 'dep_prod_id'=>$dep_prod_id),
				//'success' => "function(data) { alert('imported data to the dependent products =$dep_prod_list_str');  $('#btn_export_{$app->app_id}').attr('disabled','disabled') }"
				'success' => "function(data) { 
						alert('imported data to the dependent product $dep_prod_cid');  
							$('#btn_export_{$app->app_id}_$dep_prod_cid').attr({disabled:'disabled', value:' Exported to $dep_prod_cid'}) 
						}",
				'error' => "function(data) { 
						alert('Not able to import data to the dependent product $dep_prod_cid');  
							$('#btn_export_{$app->app_id}').attr({disabled:'disabled', value:'FAILED $dep_prod_cid'}) 
						}"
					),
				array('id'=>'btn_export_'.$app->app_id.'_'.$dep_prod_cid)
			); // end ajaxButton
		}
		$imported_str =' (already exported into:';
		foreach($already_imported_prod_id_list as $imp_prod_id){
			$imported_str .= Products::productId2Cid($imp_prod_id) .' ';
		}
		$imported_str = ' ' .trim($imported_str) .')';
		if (!empty($already_imported_prod_id_list)){
			echo $imported_str;
		}
	}
	else {
		echo '[Cannot Export Until Application is Complete]';
	}
      }
      ?>
      
      
      </span>
  
  </td>
  
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'certification_type'); ?></span></td>
  <td><span class="row">
    <?php
			switch($app->certification_type) {

				case CertificationApplications::TYPE_NEW :			echo 'New'; break;
				case CertificationApplications::TYPE_ADDITIONAL :	echo 'Additional'; break;
				case CertificationApplications::TYPE_RECERT :		echo 'Re-certification'; break;
				case CertificationApplications::TYPE_DEPENDENT :	echo 'Dependent'; break;
				case CertificationApplications::TYPE_TRANSFER :		echo 'Transfer'; break;
				default:
					echo "Unknown";
			}
		?>
    <?php echo CHtml::error($app,'certification_type'); ?></span></td>
</tr>
<?php
if ($app->certification_type == CertificationApplications::TYPE_RECERT) {
?>	

	<tr>
	<td><span class="row"><?php echo CHtml::activeLabelEx($app,'recert_type_id'); ?></span></td>
	<td><span class="row">
		<?php
				
		echo CHtml::activeDropDownList($app,"[$i]recert_type_id", $app->recertTypeIdOptionList()); ?> <?php echo CHtml::error($app,'recert_type_id'); ?></span></td>
	</tr>
	<tr>
	<td><span class="row"><?php echo CHtml::activeLabelEx($product,'recert_notes'); ?></span></td>
	<td><span class="row"><?php echo $product->recert_notes; ?></span></td>
		
	</tr>
	
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'recert_changes_hw'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]recert_changes_hw",array('rows'=>3, 'cols'=>50)); ?> <?php echo CHtml::error($app,'recert_changes_hw'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'recert_changes_fw'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]recert_changes_fw",array('rows'=>3, 'cols'=>50)); ?> <?php echo CHtml::error($app,'recert_changes_fw'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'recert_changes_sw'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]recert_changes_sw",array('rows'=>3, 'cols'=>50)); ?> <?php echo CHtml::error($app,'recert_changes_sw'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'recert_changes_os'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]recert_changes_os",array('rows'=>3, 'cols'=>50)); ?> <?php echo CHtml::error($app,'recert_changes_os'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'recert_changes_other'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]recert_changes_other",array('rows'=>3, 'cols'=>50)); ?> <?php echo CHtml::error($app,'recert_changes_other'); ?> </span></td>
</tr>
<?php
} // end if app type recert
?>

<tr>

<tr>
<td>Company Information</td>

<td>
<?php
//print $app->users->parent_id;
//print "," . $app->products->company_id;
	if ($app->users->parent_id == $app->products->company_id) {
		print "Certification of a product for my own company";
	}
	else {
		print "Certification of a product for another company";
	}
?>
</td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'date_submitted'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextField($app,"[$i]date_submitted",array("id"=>"date_submitted__$i")); ?>
    <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>"date_submitted__$i",
                    'ifFormat'=>'%m/%d/%Y',
                    //'ifFormat'=>'%Y-%m-%d',
                    'showsTime'=>false
                    ));
                ?>
  &nbsp;(calendar appears when textbox is clicked)
  <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
  </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'requested_by'); ?></span></td>
  <td><span class="row"><?php echo "".$app->users->username. ", change to:"; ?>
      <?php 
            
            $this->widget('CAutoComplete',//CAutoComplete
             array(
		//name of the html field that will be generated
		'name'=>"user_name__$i",

		//replace controller/action with real ids
		'url'=>array('certificationApplications/AutoCompleteLookupUsername'),
		'max'=>50, //specifies the max number of items to display


		//specifies the number of chars that must be entered
		//before autocomplete initiates a lookup
		'minChars'=>2,
		'delay'=>50, //number of milliseconds before lookup occurs
		'matchCase'=>false, //match case when performing a lookup?

		//any additional html attributes that go inside of
		//the input field can be defined here
		'htmlOptions'=>array('size'=>'40'),

	'methodChain'=>".result(function(event,item){\$(\"#CertificationApplications_requested_by__$i\").val(item[1]);})",
));
             
          

            echo CHtml::hiddenField("CertificationApplications[$i][requested_by]", $value, array('id'=>"CertificationApplications_requested_by__$i"));
            ?>
    <?php echo CHtml::error($app,'requested_by'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'company_contact'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextField($app,"[$i]company_contact",array('size'=>60,'maxlength'=>128)); ?> <?php echo CHtml::error($app,'company_contact'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'date_submitted'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextField($app,"[$i]date_submitted",array("id"=>"date_submitted__$i")); ?> &nbsp;(calendar appears when textbox is clicked)
      <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>"date_submitted__$i",
                    'ifFormat'=>'%m/%d/%Y',
                    //'ifFormat'=>'%Y-%m-%d',
                    'showsTime'=>false
                    ));
                ?>
      <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
  </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,"certifying_lab"); ?></span></td>
  <td><span class="row">
    <?php
			echo CHtml::activeDropDownList($app,"[$i]certifying_lab", CHtml::listData(Labs::model()->findAll(array('order'=>'company_name')), 'lab_id', 'company_name'));

		?>
    <?php echo "current lab = ".$app->labs->company_name; ?>
    <?php
		//echo $form->textField($model,'certifying_lab',array('size'=>10,'maxlength'=>10));
		?>
    <?php echo CHtml::error($app,'certifying_lab'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'date_lab_accepted'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextField($app,"[$i]date_lab_accepted",array("id"=>"date_lab_accepted__$i")); ?> &nbsp;(calendar appears when textbox is clicked)
      <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>"date_lab_accepted__$i",
                    //'ifFormat'=>'%Y-%m-%d',
                    'ifFormat'=>'%m/%d/%Y',
                    'showsTime'=>false
                    ));
                ?>
      <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
  </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'date_staff_reviewed'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextField($app,"[$i]date_staff_reviewed",array("id"=>"date_staff_reviewed__$i")); ?>
      <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>"date_staff_reviewed__$i",
                    //'ifFormat'=>'%Y-%m-%d',
                    'ifFormat'=>'%m/%d/%Y',
                    'showsTime'=>false
                    ));
                ?>
      <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
  </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'date_certified'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextField($app,"[$i]date_certified",array("id"=>"date_certified__$i")); ?>
      <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>"date_certified__$i",
                    //'ifFormat'=>'%Y-%m-%d %H:%M',
                     'ifFormat'=>'%m/%d/%Y %H:%M',
                    'showsTime'=>true
                    ));
                ?>
      <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
  </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'status'); ?></span></td>
  <td><span class="row">
    <?php

			// remove statuses later in process so that user does not
			// accidentally bypass a process
			// case 20919: superadmin should have the capability to move the status to Lab Acceptance, regardless of previous state.
			$status_list = array(
				Yii::app()->params->STATUS_STEP1 =>'1: Application',
				Yii::app()->params->STATUS_STEP2 => '2: Lab Selection',
				Yii::app()->params->STATUS_STEP3 => '3: Lab Acceptance',
				Yii::app()->params->STATUS_STEP4 => '4: Testing',
				Yii::app()->params->STATUS_STEP5 => '5: Staff Approval',
				//Yii::app()->params->STATUS_STEP6 => '6: Oversight Committee (obsolete)',
				Yii::app()->params->STATUS_COMPLETE => '7: Complete',
				Yii::app()->params->STATUS_HOLD => 'HOLD (23)',
				Yii::app()->params->STATUS_FAILED =>'FAILED (19)'
				);
				if ($app->status < Yii::app()->params->STATUS_COMPLETE) {
					for ($status_i = Yii::app()->params->STATUS_COMPLETE; $status_i > Yii::app()->params->STATUS_STEP1; $status_i--) {
						if ($app->status < $status_i && ($status_i != Yii::app()->params->STATUS_STEP3)) {
							unset($status_list[$status_i]);
						}
					}

				}

		?>
    <?php echo CHtml::activeDropDownList($app,"[$i]status", $status_list); ?> <?php echo CHtml::error($app,'status'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'staff_notes'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]staff_notes",array('rows'=>5, 'cols'=>50)); ?> <?php echo CHtml::error($app,'staff_notes'); ?></span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'committee_notes'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]committee_notes",array('rows'=>5, 'cols'=>50)); ?> <?php echo CHtml::error($app,'committee_notes'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'lab_notes'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]lab_notes",array('rows'=>5, 'cols'=>50)); ?> <?php echo CHtml::error($app,'lab_notes'); ?> </span></td>
</tr>
		<?php
			// build the OS list, lets add say 0 -- not set --
				$os_list = CHtml::listData(Os::model()->findAll(), 'os_id', 'name');
				$os_list[0] = '-- not set --';
			?>

<tr>
  <td><span class="row"> <?php echo CHtml::activeLabelEx($app, "[$i]pending_os_id"); ?></span></td>
  <td class="row"><span class="row"><?php echo CHtml::activeDropDownList($app,"[$i]pending_os_id", $os_list); ?> <?php echo CHtml::error($app,"[$i]pending_os_id"); ?> Other: <?php echo CHtml::activeTextField($app,"[$i]pending_os_other",array('size'=>32,'maxlength'=>32)); ?> <?php echo CHtml::error($app,"[$i]pending_os_other"); ?> </span> </td>
</tr>
<tr>
  <td><span class="row"> <?php echo CHtml::activeLabelEx($app, "[$i]initial_os_id"); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeDropDownList($app,"[$i]initial_os_id", $os_list); ?> <?php echo CHtml::error($app,"[$i]initial_os_id"); ?>  Other:  <?php echo CHtml::activeTextField($app,"[$i]initial_os_other",array('size'=>32,'maxlength'=>32)); ?> <?php echo CHtml::error($app,"[$i]initial_os_other"); ?> </span> </td>
</tr>

<tr>
  <td><span class="row"><label for="CertificationApplications_<?=$i?>_pending_firmware">Firmware</label></span></td>
  <!--<td><span class="row"><?php /*echo CHtml::activeTextArea($app,"[$i]pending_firmware",array('rows'=>6, 'cols'=>50)); ?> <?php echo CHtml::error($app,'pending_firmware'); */ ?> </span></td>-->
	<td><span class="row">Pending: <?php echo CHtml::activeTextField($app,"[$i]pending_firmware"); ?> Initial: <?php echo (empty($app->initial_firmware) ? '(not set)' : ''); ?></td>
</tr>
<tr>  
  <td><span class="row"><label for="CertificationApplications_<?=$i?>_pending_wirelesschipset">Wireless Chipset</label></span></td>


<td><span class="row">Pending: <?php echo CHtml::activeTextField($app,"[$i]pending_wireless_chipset"); ?> Initial: <?php echo (empty($app->initial_wireless_chipset) ? '(not set)' : ''); ?></td>	
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'initial_wireless_chipset'); ?></span></td>
  <!--<td><span class="row"><?php /*echo CHtml::activeTextArea($app,"[$i]pending_firmware",array('rows'=>6, 'cols'=>50)); ?> <?php echo CHtml::error($app,'pending_firmware'); */ ?> </span></td>-->
	<td><span class="row"><?php echo $app->initial_wireless_chipset; ?></td>
</tr>

<tr>
  <td><div class="row"> <?php echo CHtml::activeLabelEx($app,'hold'); ?></div></td>
  <td><span class="row"><?php echo CHtml::activeTextField($app,"[$i]hold"); ?> (obsolete?) <?php echo CHtml::error($app,'hold'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'publish_on'); ?></span></td>
  <td><span class="row">
    <?php
                    //$ary = array(''=>'-- choose --');
                    foreach (CertificationApplications::publishOnList() as $key => $value){
                        $ary[$key]= $value;
                    }
                    echo CHtml::activeDropDownList($app,"[$i]publish_on",$ary);
                ?>
    <?php echo CHtml::error($app,'publish_on'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'deferred_date'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextField($app,"[$i]deferred_date",array("id"=>"deferred_date__$i")); ?> &nbsp;(calendar appears when textbox is clicked)
      <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>"deferred_date__$i",
                    //'ifFormat'=>'%Y-%m-%d',
                        'ifFormat'=>'%m/%d/%Y ',

                    'showsTime'=>false
                    ));
                ?>
      <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
    <?php echo CHtml::error($app,'deferred_date'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'differences'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]differences",array('rows'=>6, 'cols'=>50)); ?> <?php echo CHtml::error($app,'differences'); ?> </span></td>
</tr>
<tr>
  <td><div class="row"> <?php echo CHtml::activeLabelEx($app,'dependent_configuration'); ?></div></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]dependent_configuration",array('rows'=>6, 'cols'=>50)); ?> <?php echo CHtml::error($app,'dependent_configuration'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'module_changes'); ?></span></td>
  <td><span class="row"><?php echo CHtml::activeTextArea($app,"[$i]module_changes",array('rows'=>6, 'cols'=>50)); ?> <?php echo CHtml::error($app,'module_changes'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'test_plan'); ?></span></td>
  <td><span class="row">
    <?php
			//used for enum types
			$schema = $app->getTableSchema()->getColumn('test_plan')->dbType;
			preg_match_all("/'([^']+)'/",$schema,$matches);
			$matches = array_combine($matches[1],$matches[1]);
			echo CHtml::activeDropDownList($app,"[$i]test_plan",$matches);

		?>
    <?php echo CHtml::error($app,'test_plan'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'test_engine'); ?></span></td>
  <td><span class="row"><?php echo ($app->test_engine == 1) ? 'Yes' : 'No'; ?></span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'frequency_band_mode'); ?></span></td>
  <td><span class="row">
    <?php
		// used for enum type fields
		$schema = $app->getTableSchema()->getColumn('frequency_band_mode')->dbType;
		preg_match_all("/'([^']+)'/",$schema,$matches);
		$matches = array_combine($matches[1],$matches[1]);
                // case 4476: don't allow 'NULL', change that to a blank value
                unset($matches['NULL']);
                $matches['']='NULL';
		echo CHtml::activeDropDownList($app,"[$i]frequency_band_mode",$matches);
		?>
    <?php echo CHtml::error($app,'frequency_band_mode'); ?> </span></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><table cellpadding="0" cellspacing="1" border="0" class="summary" style="width:auto">
	  <tr class="cert_ac_class" >
		  <td>&nbsp;</td>
		  <th colspan="2" style='text-align: center'><b>802.11n</b></th>
		  <th style='text-align: center'>&nbsp;<b>802.11ac</b>&nbsp;</th>
	</tr>
    <tr>
      <th><b>Certified Spatial Streams</b></th>
      <th><b>2.4 GHz</b></th>
      <th><b>5.0 GHz</b></th>
      <th><b>5.0 GHz</b></th>
      </tr>
    <tr>
      <td><b>Transmit (Tx)</b></td>
      <td><?php
                echo CHtml::activeDropDownList($app,"[$i]certified_tx_spatial_stream_24", range(0,Products::MAX_CERTIFIED_STREAMS));
            ?></td>
      <td><?php
                echo CHtml::activeDropDownList($app,"[$i]certified_tx_spatial_stream_50", range(0,Products::MAX_CERTIFIED_STREAMS));
            ?></td>
	  <td><?php
                echo CHtml::activeDropDownList($app,"[$i]certified_tx_spatial_stream_50_ac", range(0,Products::MAX_CERTIFIED_STREAMS));
            ?></td>
      </tr>
    <tr class="even">
      <td><b>Receive (Rx)</b></td>
      <td><?php
                echo CHtml::activeDropDownList($app,"[$i]certified_rx_spatial_stream_24", range(0,Products::MAX_CERTIFIED_STREAMS));
            ?></td>
      <td><?php
                echo CHtml::activeDropDownList($app,"[$i]certified_rx_spatial_stream_50", range(0,Products::MAX_CERTIFIED_STREAMS));
            ?></td>
	  <td><?php
                echo CHtml::activeDropDownList($app,"[$i]certified_rx_spatial_stream_50_ac", range(0,Products::MAX_CERTIFIED_STREAMS));
            ?></td>
      </tr>
    </table>
    <p><span class="hint">NOTE: changing these streams does not affect the product. </span></p></td>
</tr>
<tr>
  <td valign="top"><b>Certifications</b>:</td>
  <td>&nbsp;</td>
</tr>
<tr>
      <td colspan="2" valign="top"><?php

          // build a single dimension list of cert_id
        //$prod_cert_list = $product->product_certifications; // too slow, use another method
        $app_cert_list = $app->certificationsArray();

        $cert_id_list = array();
        foreach ($app_cert_list as $row){
            $cert_id_list[] = $row['cert_id'];
        }

    echo $this->renderPartial('_cert_checkboxes', array(
        'certificationsArray'=>$app_cert_list,
        'cert_id_list'=>$cert_id_list,
        'field_prefix'=>"CertificationApplications[$i]",
        'is_test_engine'=>$app->test_engine,
        'table_class'=>'app_cert_edit'));
        ?>      </td>
  </tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($app,'agree_single_stream'); ?></span></td>
  <td><span class="row">
    <?php
                    echo CHtml::activeDropDownList($app,"[$i]agree_single_stream", array(0=>'No', 1=>'Yes'));
                ?>
    <?php echo CHtml::error($app,'agree_single_stream'); ?> </span></td>
</tr>


</table>



