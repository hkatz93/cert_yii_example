<!-- certificationApplications edit form -->
<div class="wide form">
<?php
$js = <<<EOD
$('#user_name').change(function() {
  if ($('#user_name').val() == '') {
    $('#CertificationApplications_requested_by').val('');
  }
});
EOD;

$js2 = <<<EOD
$('#add_cert_fieldset').hide();
$('#add_cert_link').click(
    function (){
        $('#add_cert_fieldset').show();
        $('#add_cert_link').hide();
    }
);
EOD;

Yii::app()->clientScript->registerScript('removeHiddenRequestByValue', $js, CClientScript::POS_READY);
Yii::app()->clientScript->registerScript('addCertLink', $js2, CClientScript::POS_READY);
?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'certification-applications-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
        
        <div class="row">
            <label for="CeritificationApplications_product_cid">CID</label>
		
		<?php echo Products::productId2Cid($model->product_id); ?>
		
	</div>

        <div class="row">
		<?php echo $form->labelEx($model,'app_id'); ?>
		<?php echo $model->app_id; ?>
	
		<?php echo $form->error($model,'app_id'); ?>
	</div>
        

	<div class="row">
		<?php echo $form->labelEx($model,'certification_type'); ?>
		<!--<?php echo $form->textField($model,'certification_type'); ?>-->
		<?php 
			switch($model->certification_type) {
				
				case CertificationApplications::TYPE_NEW :			echo 'New'; break;
				case CertificationApplications::TYPE_ADDITIONAL :	echo 'Additional'; break;
				case CertificationApplications::TYPE_RECERT :		echo 'Re-certification'; break;
				case CertificationApplications::TYPE_DEPENDENT :	echo 'Dependent'; break;
				case CertificationApplications::TYPE_TRANSFER :		echo 'Transfer'; break;
				default: 
					echo "Unknown";
			}
		?>
		<?php echo $form->error($model,'certification_type'); ?>
	</div>

	<div class="row">
	<fieldset>
    <legend>Requested Certifications: </legend>
    <p style="font-weight: bold">Check to delete</p>
	<?php 
            /*
            $req_certs=RequestedCertifications::model()->with(
                'certifications',
                'test_results',
                'test_data'
              )->findAll('app_id='.$model->app_id);
          
                $i =0;
                foreach ($req_certs as $c) {
                    if ($c->cert_id == Certifications::CERT_WPS_PIN){
                        //echo "<b>Wi-Fi Protected Setup&trade;1</b> <br />";
                    }
                    if ($c->cert_id == Certifications::CERT_WPS2_PIN){
                        //echo "<b>Wi-Fi Protected Setup&trade;2</b> <br />";
                    }                   
                    $i++;
		}
              */
	?>
    <div class="row">
        <table class="close-line-table" style="width:auto; border-collapse: collapse; border-spacing: 0px; padding: 0 0 0 0;" cellspacing="0">
            <tr><td>Certification:</td><td>Test Result:</td><td>&nbsp;</td></tr>
         <?php
          $i = 0;
          $test_result_option_ary = TestResults::resultsDropDownArray();

          $rows = $model->certificationsArray();

          //foreach ($req_certs as $c) {
          foreach ($rows as $row) {
              // calculate whether has test data
              $td = TestData::model()->find('request_id=:request_id', array('request_id'=>$row['request_id']));            
              $has_test_data = (!empty($td->data_id)) ? true : false;

          ?>

            <tr><td>
                <?php
                    // make sure we cannot delete cert abg if it has any lab results
                    // we would need special code, like for cert n, to move the results to another cert
                    //
                    $disable_cert_delete = false;
                    if (in_array($row['cert_id'], array(
                        Certifications::CERT_A,
                        Certifications::CERT_A_TEST_ENGINE,
                        Certifications::CERT_B,
                        Certifications::CERT_B_TEST_ENGINE,
                        Certifications::CERT_G,
                        Certifications::CERT_G_TEST_ENGINE,
                        Certifications::CERT_N,
                        
                        ))
                    && $has_test_data) {
                        $disable_cert_delete = true;
                    }
                    if ($row['cert_id'] == Certifications::CERT_WPS_PIN){
                        echo "<p style='margin: 0.25em 0 0.25em 0'><b>Wi-Fi Protected Setup&trade;1</b> </p>";
                    }
                    if ($row['cert_id'] == Certifications::CERT_WPS2_PIN){
                        echo "<p style='margin: 0.25em 0 0.25em 0'><b>Wi-Fi Protected Setup&trade;2</b> </p>";
                    }
                    $chk_ary = array('value'=>$row['request_id'],'id'=>"delete_request_id$i");
                    if ($disable_cert_delete){
                        $chk_ary['disabled']='disabled';
                    }
                    echo CHtml::checkBox('delete_request_id[]',false, $chk_ary);
                    echo " ".$row['display_name'];
                    $i++;
                ?>
                    
                </td>
                
                <td style="text-align: center">
                    <?php
                       echo (!empty($test_result_option_ary[$row['result']])
                               ? $test_result_option_ary[$row['result']]
                               : 'N/A');
                    ?>
                </td>
                <td>
                    <?php
                    if ($has_test_data){
                        echo CHtml::link('Edit Data', array('productLabResults/index', 'id'=>$row['request_id']));
                    }
                    //echo CHtml::checkBox('delete_request_id[]',false, array('value'=>$c->request_id,'id'=>"delete_request_id$i"));
                    
                    ?>
                </td>

            </tr>
            
          <?php
            $i++;
          } // end foreach for table row
          ?>

        </table>
        <?php
        echo CHtml::submitButton('Delete Certification(s)', array('name'=>'action_delete_certifications','confirm'=>'Delete checked certifications from this application?'));
        ?>
        
    </div>
    </fieldset>
            <p><a id="add_cert_link">Add Certification</a></p>
    <fieldset id="add_cert_fieldset">
    <legend>Add a Certification: </legend>
    <div class="row">
        <table style="width:auto">
            <tr><td>Certification:</td><td>Test Result:</td><td>&nbsp;</td></tr>
            <tr><td>
                    <?php
                    $data = CHtml::listData(Certifications::getDropDownArray(array('is_test_engine'=>$model->test_engine)),'id','text','group');
                    array_unshift($data, array('text'=>'-- choose one --'));
                    echo CHtml::dropDownList('add_cert[id]','category',$data, array('encode'=>false));
                    ?>
                </td>
                    
                <td>
                    <?php
                    echo CHtml::dropDownList('add_cert[result]', TestResults::PASS, TestResults::resultsDropDownArray());
                    ?>
                </td>
                <td><?php
                    echo CHtml::submitButton('Add Certification', array('name'=>'action_add_certification','confirm'=>'Add Certification to this application?'));
                    ?>
                </td>
            </tr>
        </table>
        <p class="hint">Currently, there are no restrictions to the certifications you are able to add.</p>
    </div>
    </fieldset>


	</div>
        <!--
        <div class="row">
            <?php $cid = Products::productId2Cid($model->product_id); ?>
            <fieldset>
                <legend>Product Certifications for <?php echo $cid; ?>:</legend>
                <?php
                    $cid = Products::productId2Cid($model->product_id);
                    //$prod_certs = ProductCertifications::model()->find('cid=:cid', array(':cid'=>"WFA$product_id")); // why is this not working?
                   $prod_certs = ProductCertifications::model()->findAll("cid='$cid'"); 

                   if (count($prod_certs)==0){
                       echo "No published certifications exist yet";
                   }
                   else {
                        foreach ($prod_certs as $pc){
                            echo "cert_id = ".$pc->cert_id.",";
                            //echo "display_name = ".$pc->display_name.",";
                        }
                   }
                ?>
            </fieldset>

        </div>
        -->
        <div class="row">
            <?php echo $form->labelEx($model,'requested_by'); ?>
            <?php echo "".$model->users->username. ", change to:"; ?>
            <?php $this->widget('CAutoComplete',//CAutoComplete
             array(
		//name of the html field that will be generated
		'name'=>'user_name',

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

	'methodChain'=>".result(function(event,item){\$(\"#CertificationApplications_requested_by\").val(item[1]);})",
));
            ?>
             
        <?php echo $form->hiddenField($model,'requested_by',array('size'=>20,'maxlength'=>10)); ?>
	<?php echo $form->error($model,'requested_by'); ?>
	
        </div>

	<div class="row">
		<?php echo $form->labelEx($model,'company_contact'); ?>
		<?php echo $form->textField($model,'company_contact',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'company_contact'); ?>
	</div>

        <!--
	<div class="row">
		<?php //echo $form->labelEx($model,'date_submitted'); ?>
		<?php //echo $form->textField($model,'date_submitted'); ?>
		<?php //echo $form->error($model,'date_submitted'); ?>
	</div>
        -->
        <div class="row">
        
                <?php echo CHtml::activeLabelEx($model,'date_submitted'); ?>
                <?php echo CHtml::activeTextField($model,'date_submitted',array("id"=>"date_submitted")); ?>
                &nbsp;(calendar appears when textbox is clicked)
                <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_submitted',
                    'ifFormat'=>'%m/%d/%Y',
                    //'ifFormat'=>'%Y-%m-%d',
                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
        </div>
	<div class="row">
		<?php echo $form->labelEx($model,'certifying_lab'); ?>
		<?php 
			echo $form->dropDownList($model,'certifying_lab', CHtml::listData(Labs::model()->findAll(array('order'=>'company_name')), 'lab_id', 'company_name')); 

		?>
		<?php echo "current lab = ".$model->labs->company_name; ?>
		<?php 
		//echo $form->textField($model,'certifying_lab',array('size'=>10,'maxlength'=>10)); 
		?>
		<?php echo $form->error($model,'certifying_lab'); ?>
	</div>

    <div class="row">
        
		<?php echo CHtml::activeLabelEx($model,'date_lab_accepted'); ?>
		<?php echo CHtml::activeTextField($model,'date_lab_accepted',array("id"=>"date_lab_accepted")); ?>
		&nbsp;(calendar appears when textbox is clicked)
		<?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_lab_accepted',
                    //'ifFormat'=>'%Y-%m-%d',
                    'ifFormat'=>'%m/%d/%Y',
                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
    </div>



	<div class="row">
		
		<?php echo CHtml::activeLabelEx($model,'date_finalized_results'); ?>
		<?php echo CHtml::activeTextField($model,'date_finalized_results',array("id"=>"date_finalized_results")); ?>
		
		<?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_finalized_results',
                    //'ifFormat'=>'%Y-%m-%d',
                    'ifFormat'=>'%m/%d/%Y',
                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
  

	</div>

	<div class="row">

		<?php echo CHtml::activeLabelEx($model,'date_staff_reviewed'); ?>
		<?php echo CHtml::activeTextField($model,'date_staff_reviewed',array("id"=>"date_staff_reviewed")); ?>
		
		<?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_staff_reviewed',
                    //'ifFormat'=>'%Y-%m-%d',
                    'ifFormat'=>'%m/%d/%Y',
                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
  
	</div>

	<div class="row">
		

		<?php echo CHtml::activeLabelEx($model,'date_certified'); ?>
		<?php echo CHtml::activeTextField($model,'date_certified',array("id"=>"date_certified")); ?>
		
		<?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_certified',
                    //'ifFormat'=>'%Y-%m-%d %H:%M',
                     'ifFormat'=>'%m/%d/%Y %H:%M',
                    'showsTime'=>true
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->

	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
	
		<?php 
		
			// remove statuses later in process so that user does not
			// accidentally bypass a process
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
				if ($model->status < Yii::app()->params->STATUS_COMPLETE) {
					for ($i = Yii::app()->params->STATUS_COMPLETE; $i > Yii::app()->params->STATUS_STEP1; $i--) {
						if ($model->status < $i) {
							unset($status_list[$i]);
						}
					}

				}
			
		?>
		<?php echo $form->dropDownList($model,'status', $status_list); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'staff_notes'); ?>
		<?php echo $form->textArea($model,'staff_notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'staff_notes'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'committee_notes'); ?>
		<?php echo $form->textArea($model,'committee_notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'committee_notes'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'lab_notes'); ?>
		<?php echo $form->textArea($model,'lab_notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'lab_notes'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'hold'); ?>
		<?php echo $form->textField($model,'hold'); ?> (obsolete?)
		<?php echo $form->error($model,'hold'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'publish_on'); ?>
		<?php
                    //$ary = array(''=>'-- choose --');
                    foreach (CertificationApplications::publishOnList() as $key => $value){
                        $ary[$key]= $value;
                    }
                    echo $form->dropDownList($model,'publish_on',$ary);
                ?>
		<?php echo $form->error($model,'publish_on'); ?>
	</div>

	<div class="row">

		<!--<?php echo $form->textField($model,'deferred_date'); ?>-->


		      
        
                <?php echo CHtml::activeLabelEx($model,'deferred_date'); ?>
                <?php echo CHtml::activeTextField($model,'deferred_date',array("id"=>"deferred_date")); ?>
                &nbsp;(calendar appears when textbox is clicked)
                <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'deferred_date',
                    //'ifFormat'=>'%Y-%m-%d',
                        'ifFormat'=>'%m/%d/%Y ',

                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
				<?php echo $form->error($model,'deferred_date'); ?>
        </div>

	

	<div class="row">
		<?php echo $form->labelEx($model,'differences'); ?>
		<?php echo $form->textArea($model,'differences',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'differences'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'dependent_configuration'); ?>
		<?php echo $form->textArea($model,'dependent_configuration',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'dependent_configuration'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'module_changes'); ?>
		<?php echo $form->textArea($model,'module_changes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'module_changes'); ?>
	</div>
<!--
	<div class="row">
		<?php echo $form->labelEx($model,'auto_delete'); ?>
		<?php echo $form->textField($model,'auto_delete'); ?>
		<?php echo $form->error($model,'auto_delete'); ?>
	</div>
-->
	<div class="row">
		<?php echo $form->labelEx($model,'test_plan'); ?>
		<?php
			//used for enum types
			$schema = $model->getTableSchema()->getColumn('test_plan')->dbType;
			preg_match_all("/'([^']+)'/",$schema,$matches);
			$matches = array_combine($matches[1],$matches[1]);
			echo $form->dropDownList($model,'test_plan',$matches);

		?>


		<?php echo $form->error($model,'test_plan'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'test_engine'); ?>
                <?php echo ($model->test_engine == 1) ? 'Yes' : 'No'; ?>
		<!--<?php echo $form->textField($model,'test_engine'); ?>-->
		<?php echo $form->error($model,'test_engine'); ?>
	</div>

	<div class="row">

		<?php echo $form->labelEx($model,'frequency_band_mode'); ?>
		<?php
		// used for enum type fields
		$schema = $model->getTableSchema()->getColumn('frequency_band_mode')->dbType;
		preg_match_all("/'([^']+)'/",$schema,$matches);
		$matches = array_combine($matches[1],$matches[1]);
		echo $form->dropDownList($model,'frequency_band_mode',$matches);
		?>

		<?php echo $form->error($model,'frequency_band_mode'); ?>
	</div>

	
<div class="row">
    <!-- start supported spacial streams -->
    <table cellpadding="0" cellspacing="1" border="0" class="summary" style="width:auto">
        <tr>
            <th><b>Certified Spatial Streams</b></th>
            <th><b>2.4 GHz</b></th>
            <th><b>5.0 GHz</b></th>
        </tr>
        <tr>
            <td><b>Transmit (Tx)</b></td>
            <td>
            <?php
                echo $form->dropDownList($model,'certified_tx_spatial_stream_24', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>
            </td>
            <td>
            <?php
                echo $form->dropDownList($model,'certified_tx_spatial_stream_50', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>

            </td>
        </tr>

        <tr class="even">
            <td><b>Receive (Rx)</b></td>
            <td>
            <?php
                echo $form->dropDownList($model,'certified_rx_spatial_stream_24', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>

            </td>
            <td>
            <?php
                echo $form->dropDownList($model,'certified_rx_spatial_stream_50', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>
            </td>
        </tr>
    </table>
<p class="hint">NOTE: changing these streams does not affect the product. </p>
    
</div>

	<div class="row">
		<?php echo $form->labelEx($model,'agree_single_stream'); ?>
		
                <?php
                    echo $form->dropDownList($model,'agree_single_stream', array(0=>'No', 1=>'Yes'));
                ?>

		<?php echo $form->error($model,'agree_single_stream'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('confirm'=>'Save these changes?')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- certificationApplications edit form -->