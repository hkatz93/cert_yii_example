<?php
$js2 = <<<EOD
$('#add_cert_fieldset').hide();
$('#add_cert_link').click(
    function (){
        $('#add_cert_fieldset').show();
        $('#add_cert_link').hide();
    }
);
EOD;

Yii::app()->clientScript->registerScript('addCertLink', $js2, CClientScript::POS_READY);
?>
<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'products-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

        <div class="row">
		<?php echo $form->labelEx($model,'cid'); ?>
                <?php echo $model->cid; ?>
		<!--<?php echo $form->textField($model,'cid',array('size'=>16,'maxlength'=>16)); ?>-->
		<?php echo $form->error($model,'cid'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'product_name'); ?>
		<?php echo $form->textField($model,'product_name',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'product_name'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'company_id'); ?>
                <!--<?php echo $model->companies->company_name; ?>-->
		<!--<?php echo $form->textField($model,'company_id',array('size'=>10,'maxlength'=>10)); ?>-->
                <?php
                //echo $form->dropDownList($model,'publish_on',array('Certification Date'=>'Certification Date', 'Never'=>'Never', 'Deferred Date'=>'Deferred Date'));
                //$active_companies = Companies::model()->active()->findAll();
                //$co_ary = array();
                //foreach ($active_companies as $c) {
                //    $co_ary["{$c->company_id}"] = $c->company_name." (".$c->company_id.")";
                //}
                //echo $form->dropDownList($model,'company_id',$co_ary);

                // display only active companies plus the current one just in case this company is not active
                echo $form->dropDownList($model,'company_id', CHtml::listData($model->companies->active_plus_current()->findAll(), 'company_id', 'company_name'));

                ?>
		<?php echo $form->error($model,'company_id'); ?>
	</div>
        <div class="row">
            Contact information:
            <?php ?>
        </div>

	

	
        <div class="row">
            <?php $cid = Products::productId2Cid($model->product_id); ?>
            <fieldset>
                <legend>Published Certifications for <?php echo $cid; ?>:</legend>
                <?php
                    $cid = Products::productId2Cid($model->product_id);
                    
                   //$prod_certs = ProductCertifications::model()->findAll("cid='$cid'");
                   //$prod_certs = ProductCertifications::model()->with('certifications')->findAll("cid='$cid'");
                   //$prod_certs = ProductCertifications::model()->with('certifications')->findAll("cid=:cid", array(':cid'=>$cid));

                   /*
                    $i = 0;
                    foreach ($prod_certs as $pc){
                        if ($pc->cert_id == Certifications::CERT_WPS_PIN){
                            echo "<b>Wi-Fi Protected Setup&trade;1</b> <br />";
                        }
                        if ($pc->cert_id == Certifications::CERT_WPS2_PIN){
                            echo "<b>Wi-Fi Protected Setup&trade;2</b> <br />";
                        }
                        echo CHtml::checkBox('delete_product_certification_id[]',false, array('value'=>$pc->product_certification_id,'id'=>"delete_product_certification_id$i"));
                        echo " ".$pc->certifications->display_name."<br/>";
                        //echo "display_name = ".$pc->display_name.",";
                        $i++;
                    }
*/
                    $i = 0;
                    $rows = $model->certificationsArray();
                    //print_r($rows);
                    foreach ($rows as $row){
        
                        if ($row['cert_id'] == Certifications::CERT_WPS_PIN){
                        echo "<p style='margin: 0.25em 0 0.25em 0'><b>Wi-Fi Protected Setup&trade;1</b> </p>";
                        }
                        if ($row['cert_id'] == Certifications::CERT_WPS2_PIN){
                            echo "<p style='margin: 0.25em 0 0.25em 0'><b>Wi-Fi Protected Setup&trade;2</b> </p>";
                        }
                        // make sure to disallow removing of cert abg
                        // currently there is no code to move lab results yet
                        $chkbox_ary = array('value'=>$row['product_certification_id'],'id'=>"delete_product_certification_id$i");
                        if (in_array($row['cert_id'], array(
                                Certifications::CERT_A,
                                Certifications::CERT_A_TEST_ENGINE,
                                Certifications::CERT_B,
                                Certifications::CERT_B_TEST_ENGINE,
                                Certifications::CERT_G,
                                Certifications::CERT_G_TEST_ENGINE
                            ))) {
                            $chkbox_ary['disabled'] = 'disabled';
                        }
                        echo CHtml::checkBox('delete_product_certification_id[]',false, $chkbox_ary);
                        echo " ".$row['display_name']."<br/>";
                        $i++;
                    }
                ?>
                <?php
        echo CHtml::submitButton('Delete Certification(s)', array('name'=>'action_delete_certifications','confirm'=>'Delete checked certifications from this application?'));
        ?>
            </fieldset>

        </div>
<p><a id="add_cert_link">Add Certification</a></p>
<fieldset id="add_cert_fieldset">
    <legend>Publish a Certification: </legend>
    <div class="row">
        <table style="width:auto">
            <tr><td>Certification:</td><td>&nbsp;</td></tr>
            <tr><td>
                    <?php
                    $data = CHtml::listData(Certifications::getDropDownArray(),'id','text','group');
                    array_unshift($data, array('text'=>'-- choose one --'));
                    echo CHtml::dropDownList('add_cert[id]','category',$data, array('encode'=>false));
                    ?>
                </td>

                
                <td><?php
                    echo CHtml::submitButton('Add Certification', array('name'=>'action_add_certification','confirm'=>'Add Certification to this application?'));
                    ?>
                </td>
            </tr>
        </table>
        <p class="hint">Adding a certification here will publish it immediately if a completed application exists.</p>
    </div>
    </fieldset>


	

	<div class="row">
		<?php echo $form->labelEx($model,'model_number'); ?>
		<?php echo $form->textField($model,'model_number',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'model_number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sku'); ?>
		<?php echo $form->textField($model,'sku',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'sku'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'additional_skus'); ?>
		<?php echo $form->textArea($model,'additional_skus',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'additional_skus'); ?>
	</div>

<div class="row">
		<?php echo $form->labelEx($model,'os_id'); ?>
		
                <?php echo $form->dropDownList($model,'os_id', CHtml::listData(Os::model()->findAll(), 'os_id', 'name')); ?>
		<?php echo $form->error($model,'os_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'firmware'); ?>
		<?php echo $form->textField($model,'firmware',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'firmware'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'wireless_chipset'); ?>
		<?php echo $form->textField($model,'wireless_chipset',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'wireless_chipset'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type_id'); ?>
		
                <?php echo $form->dropDownList($model,'type_id', CHtml::listData(DeviceTypes::model()->findAll(), 'type_id', 'name')); ?>
		<?php echo $form->error($model,'type_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category_id'); ?>
               <!--<?php echo $model->product_categories->category; ?>-->

		<!--<?php echo $form->textField($model,'category_id',array('size'=>10,'maxlength'=>10)); ?>-->
                <?php echo $form->dropDownList($model,'category_id', CHtml::listData(ProductCategories::model()->findAll(), 'category_id', 'category')); ?>
		<?php echo $form->error($model,'category_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category_other'); ?>
		<?php echo $form->textField($model,'category_other',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'category_other'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'is_module'); ?>
		
                <?php echo $form->dropDownList($model,'is_module', array(0=>'No',1=>'Yes')); ?>
		<?php echo $form->error($model,'is_module'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'is_asd'); ?>
                <?php echo $form->dropDownList($model,'is_asd', array(0=>'No',1=>'Yes')); ?>
		
		<?php echo $form->error($model,'is_asd'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'asd_test_plan'); ?>
		<?php echo $form->textField($model,'asd_test_plan',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'asd_test_plan'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'is_dependent'); ?>

                <?php echo $form->dropDownList($model,'is_dependent', array(0=>'No',1=>'Yes')); ?>
		<?php echo $form->error($model,'is_dependent'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'parent_id'); ?>
		<?php echo $form->textField($model,'parent_id',array('size'=>10,'maxlength'=>10)); ?> (0 = no parent product)
		<?php echo $form->error($model,'parent_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'product_url'); ?>
		<?php echo $form->textField($model,'product_url',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'product_url'); ?>
	</div>

        
	<div class="row">
		<?php echo $form->labelEx($model,'transfer_source'); ?>
		<?php echo $form->textField($model,'transfer_source'); ?>
		<?php echo $form->error($model,'transfer_source'); ?>
	</div>

	

	<div class="row">
		<?php echo $form->labelEx($model,'cloned_from'); ?>
		<?php echo $form->textField($model,'cloned_from',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'cloned_from'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'obsolete'); ?>
		
            <?php echo $form->dropDownList($model,'obsolete', array(0=>'No',1=>'Yes')); ?>
		<?php echo $form->error($model,'obsolete'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'product_notes'); ?>
		<?php echo $form->textArea($model,'product_notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'product_notes'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'publish_on'); ?>
		
                <?php
                    
                    echo $form->dropDownList($model,'publish_on', $model->publishOnList()); ?>
		<?php echo $form->error($model,'publish_on'); ?>
	</div>

	<div class="row">
		
            <?php echo CHtml::activeLabelEx($model,'deferred_date'); ?>
                <?php echo CHtml::activeTextField($model,'deferred_date',array("id"=>"deferred_date")); ?>
                &nbsp;(calendar appears when textbox is clicked)
                <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'deferred_date',
                    //'ifFormat'=>'%Y-%m-%d',
                    'ifFormat'=>'%m/%d/%Y',
                    'showsTime'=>false
                    ));
                ?>
		<?php echo $form->error($model,'deferred_date'); ?>
	</div>

	

	<div class="row">
		<?php echo $form->labelEx($model,'admin_override_display'); ?>
		
                <?php echo $form->dropDownList($model,'admin_override_display',array(0=>'No',1=>'Yes')); ?> 
		<?php echo $form->error($model,'admin_override_display'); ?>
	</div>
<!--
	<div class="row">
		<?php echo $form->labelEx($model,'supported_tx_spatial_stream_24'); ?>
		<?php echo $form->textField($model,'supported_tx_spatial_stream_24'); ?>
		<?php echo $form->error($model,'supported_tx_spatial_stream_24'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'supported_rx_spatial_stream_24'); ?>
		<?php echo $form->textField($model,'supported_rx_spatial_stream_24'); ?>
		<?php echo $form->error($model,'supported_rx_spatial_stream_24'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'supported_tx_spatial_stream_50'); ?>
		<?php echo $form->textField($model,'supported_tx_spatial_stream_50'); ?>
		<?php echo $form->error($model,'supported_tx_spatial_stream_50'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'supported_rx_spatial_stream_50'); ?>
		<?php echo $form->textField($model,'supported_rx_spatial_stream_50'); ?>
		<?php echo $form->error($model,'supported_rx_spatial_stream_50'); ?>
	</div>
-->
        <div class="row">
    <!-- start supported spacial streams -->
    <table cellpadding="0" cellspacing="1" border="0" class="summary" style="width:auto">
        <tr>
            <th><b>Supported Spatial Streams</b></th>
            <th><b>2.4 GHz</b></th>
            <th><b>5.0 GHz</b></th>
        </tr>
        <tr>
            <td><b>Transmit (Tx)</b></td>
            <td>
            <?php
                echo $form->dropDownList($model,'supported_tx_spatial_stream_24', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>
            </td>
            <td>
            <?php
                echo $form->dropDownList($model,'supported_tx_spatial_stream_50', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>

            </td>
        </tr>

        <tr class="even">
            <td><b>Receive (Rx)</b></td>
            <td>
            <?php
                echo $form->dropDownList($model,'supported_rx_spatial_stream_24', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>

            </td>
            <td>
            <?php
                echo $form->dropDownList($model,'supported_rx_spatial_stream_50', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>
            </td>
        </tr>
    </table>


</div>
<!--
	<div class="row">
		<?php echo $form->labelEx($model,'certified_tx_spatial_stream_24'); ?>
		<?php echo $form->textField($model,'certified_tx_spatial_stream_24'); ?>
		<?php echo $form->error($model,'certified_tx_spatial_stream_24'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'certified_rx_spatial_stream_24'); ?>
		<?php echo $form->textField($model,'certified_rx_spatial_stream_24'); ?>
		<?php echo $form->error($model,'certified_rx_spatial_stream_24'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'certified_tx_spatial_stream_50'); ?>
		<?php echo $form->textField($model,'certified_tx_spatial_stream_50'); ?>
		<?php echo $form->error($model,'certified_tx_spatial_stream_50'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'certified_rx_spatial_stream_50'); ?>
		<?php echo $form->textField($model,'certified_rx_spatial_stream_50'); ?>
		<?php echo $form->error($model,'certified_rx_spatial_stream_50'); ?>
	</div>
-->
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

<p class="hint">(Note: changing certified streams will also change the most recent application with a 802.11n certification.)</p>

</div>

	<div class="row">
                <?php echo $form->labelEx($model,'external_registrar_support'); ?> 
                <?php
            // used for enum type fields
		$schema = $model->getTableSchema()->getColumn('external_registrar_support')->dbType;
		preg_match_all("/'([^']+)'/",$schema,$matches);
		$matches = array_combine($matches[1],$matches[1]);
		echo $form->dropDownList($model,'external_registrar_support',$matches);
		?>
		
		<?php echo $form->error($model,'external_registrar_support'); ?>
            <p class="hint">(Note: product must also have WPS2 certification for External Registrar support to show up.)</p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'internal_registrar_support'); ?>
		
                <?php echo $form->dropDownList($model,'internal_registrar_support',array(0=>'None',1=>'Internal Registrar')); ?>
		<?php echo $form->error($model,'internal_registrar_support'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('confirm'=>'Save these changes?')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->