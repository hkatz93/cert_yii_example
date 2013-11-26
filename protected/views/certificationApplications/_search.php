<div class="wide form">
<?php
$js = <<<EOD
$('#user_name').change(function() {
  //alert('change made to requested_by. value = '+$('#user_name').val());
  if ($('#user_name').val() == '') {
    $('#CertificationApplications_requested_by').val('');
  }
});
EOD;

Yii::app()->clientScript->registerScript('removeHiddenRequestByValue', $js, CClientScript::POS_READY);
?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'app_id'); ?>
		<?php echo $form->textField($model,'app_id',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'product_id'); ?>
		<?php echo $form->textField($model,'product_id',array('size'=>10,'maxlength'=>10)); ?> (this is like the CID without the WFA prefix)
	</div>

	<div class="row">
		<?php echo $form->label($model,'certification_type'); ?>
		<!--<?php echo $form->textField($model,'certification_type'); ?>-->
            <?php
                $cert_type_options = array(''=>'-- choose --');
                foreach (CertificationApplications::typeOptionList() as $key=>$value){
                    $cert_type_options[$key]=$value;
                }
                echo $form->dropDownList($model,'certification_type', $cert_type_options);
            ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recert_type_id'); ?>
		<!--<?php echo $form->textField($model,'recert_type_id'); ?>-->
            <?php
                $recert_type_id_options = array(''=>'-- choose --');
                foreach (CertificationApplications::recertTypeIdOptionList() as $key=>$value){
                    $recert_type_id_options[$key]=$value;
                }
                echo $form->dropDownList($model,'recert_type_id', $recert_type_id_options);
            ?>
	</div>
	
	<div class="row">
            <?php echo $form->labelEx($model,'requested_by'); ?>
            
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

            <?php $this->widget('CAutoComplete',//CAutoComplete
             array(
		//name of the html field that will be generated
		'name'=>'CertificationApplications[company_contact]',

		//replace controller/action with real ids
		'url'=>array('certificationApplications/AutoCompleteLookupCompanyContact'),
		'max'=>50, //specifies the max number of items to display


		//specifies the number of chars that must be entered
		//before autocomplete initiates a lookup
		'minChars'=>2,
		'delay'=>50, //number of milliseconds before lookup occurs
		'matchCase'=>false, //match case when performing a lookup?

		//any additional html attributes that go inside of
		//the input field can be defined here
		'htmlOptions'=>array('size'=>'40'),

	
));
            ?>

        
	<?php echo $form->error($model,'company_contact'); ?>

        </div>
<!--
	<div class="row">
		<?php echo $form->label($model,'company_contact'); ?>
		<?php echo $form->textField($model,'company_contact',array('size'=>60,'maxlength'=>128)); ?>
	</div>
-->
	<div class="row">
		<?php echo $form->label($model,'date_submitted'); ?>
		<?php echo $form->textField($model,'date_submitted'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'certifying_lab'); ?>
		<?php
                    $cert_lab_ary = array(''=>'-- choose --');
                    foreach(CHtml::listData(Labs::model()->findAll(array('order'=>'company_name')), 'lab_id', 'company_name') as $key=>$value){
                        $cert_lab_ary[$key]=$value;
                    }
                    
                    echo $form->dropDownList($model,'certifying_lab', $cert_lab_ary);
		?>
	</div>
	    <div class="row">

		<?php echo CHtml::activeLabelEx($model,'date_lab_accepted'); ?>
		<?php echo CHtml::activeTextField($model,'date_lab_accepted',array("id"=>"date_lab_accepted")); ?>
		&nbsp;(calendar appears when textbox is clicked)
		<?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_lab_accepted',
                    'ifFormat'=>'%Y-%m-%d',
                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
    </div>


	<div class="row">

		<?php echo CHtml::activeLabelEx($model,'date_finalized_results'); ?>
		<?php echo CHtml::activeTextField($model,'date_finalized_results',array("id"=>"date_finalized_results")); ?>
		&nbsp;(calendar appears when textbox is clicked)
		<?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_finalized_results',
                    'ifFormat'=>'%Y-%m-%d',
                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->


	</div>

	<div class="row">

		<?php echo CHtml::activeLabelEx($model,'date_staff_reviewed'); ?>
		<?php echo CHtml::activeTextField($model,'date_staff_reviewed',array("id"=>"date_staff_reviewed")); ?>
		&nbsp;(calendar appears when textbox is clicked)
		<?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_staff_reviewed',
                    'ifFormat'=>'%Y-%m-%d',
                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->

	</div>

        <div class="row">


		<?php echo CHtml::activeLabelEx($model,'date_certified'); ?>
		<?php echo CHtml::activeTextField($model,'date_certified',array("id"=>"date_certified")); ?>
		&nbsp;(calendar appears when textbox is clicked)
		<?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'date_certified',
                    'ifFormat'=>'%Y-%m-%d %H:%M',
                    'showsTime'=>true
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->

	</div>
	<div class="row">
		<?php echo $form->label($model,'status'); ?>
                <?php
                    $status_ary = array(''=>'-- choose --');
                    foreach (CertificationApplications::statusList() as $key=>$value){
                        $status_ary[$key]=$value;
                    }
                    echo $form->dropDownList($model,'status', $status_ary);
                    
                ?>
		<!--<?php echo $form->textField($model,'status'); ?>-->
                
	</div>

	<div class="row">
		<?php echo $form->label($model,'staff_notes'); ?>
		<?php echo $form->textArea($model,'staff_notes',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'committee_notes'); ?>
		<?php echo $form->textArea($model,'committee_notes',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'lab_notes'); ?>
		<?php echo $form->textArea($model,'lab_notes',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hold'); ?>
		<?php echo $form->textField($model,'hold'); ?>
	</div>

        <div class="row">
		<?php echo $form->labelEx($model,'publish_on'); ?>
                <?php
                    $ary = array(''=>'-- choose --');
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
                    'ifFormat'=>'%Y-%m-%d',
                    'showsTime'=>false
                    ));
                ?>
                <!-- return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true); -->
				<?php echo $form->error($model,'deferred_date'); ?>
        </div>


	<div class="row">
		<?php echo $form->label($model,'differences'); ?>
		<?php echo $form->textArea($model,'differences',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dependent_configuration'); ?>
		<?php echo $form->textArea($model,'dependent_configuration',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'module_changes'); ?>
		<?php echo $form->textArea($model,'module_changes',array('rows'=>6, 'cols'=>50)); ?>
	</div>
<!--
	<div class="row">
		<?php echo $form->label($model,'auto_delete'); ?>
		<?php echo $form->textField($model,'auto_delete'); ?>
	</div>
-->
        <div class="row">
		<?php echo $form->labelEx($model,'test_plan'); ?>
		<?php
			//used for enum types
			$schema = $model->getTableSchema()->getColumn('test_plan')->dbType;
			preg_match_all("/'([^']+)'/",$schema,$matches);
			$matches = array_combine($matches[1],$matches[1]);
                        $ary = array(''=>'-- choose --');
                        foreach($matches as $key => $value){
                            $ary[$key]=$value;
                        }
			echo $form->dropDownList($model,'test_plan',$ary);

		?>


		<?php echo $form->error($model,'test_plan'); ?>
	</div>
	<div class="row">
		<?php echo $form->label($model,'test_engine'); ?>
		<?php
                    echo $form->dropDownList($model,'test_engine', array(''=>'-- choose --', 0=>'No', 1=>'Yes'));
                    
                ?>
	</div>

	<div class="row">

		<?php echo $form->labelEx($model,'frequency_band_mode'); ?>
		<?php
		// used for enum type fields
		$schema = $model->getTableSchema()->getColumn('frequency_band_mode')->dbType;
		preg_match_all("/'([^']+)'/",$schema,$matches);
		$matches = array_combine($matches[1],$matches[1]);
                $ary = array(''=>'-- choose --');
                foreach($matches as $key => $value){
                    $ary[$key]=$value;
                }
		echo $form->dropDownList($model,'frequency_band_mode',$ary);
		?>

		<?php echo $form->error($model,'frequency_band_mode'); ?>
	</div>

<!--
<div class="row">
   
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
-->
	<div class="row">
		<?php echo $form->label($model,'agree_single_stream'); ?>
		<?php
                    echo $form->dropDownList($model,'agree_single_stream', array(''=>'-- choose --', 0=>'No', 1=>'Yes'));
                ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->