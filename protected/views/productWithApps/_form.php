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

//Yii::app()->clientScript->registerScript('addCertLink', $js2, CClientScript::POS_READY);

// fix apps so its an array
$apps = (is_array($apps)) ? $apps : array($apps);
?>
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
<p><span class="wide form"><?php echo CHtml::beginForm(); ?>
 <?php echo CHtml::errorSummary($product); ?>
    <!-- error summary for each of the applications here -->
    <?php
    
	
    foreach($apps as $i=>$app) {
        echo CHtml::errorSummary($app);
     }
?>
</span></p>
<table class='product_cert_edit_table' width="281" height="32" border="1">
<tr>
  <td>&nbsp;</td>
  <td class="data_cell">&nbsp;</td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activelabelEx($product,'product_id'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo $product->product_id ?></span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activelabelEx($product,'cid'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo $product->cid; ?></span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activelabelEx($product,'product_name'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'product_name',array('size'=>60,'maxlength'=>128)); ?> <?php echo CHtml::error($product, 'product_name'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'company_id'); ?></span></td>
  <td class="data_cell"><span class="row">
    <?php
                //echo CHtml::activeDropDownList($product,'publish_on',array('Certification Date'=>'Certification Date', 'Never'=>'Never', 'Deferred Date'=>'Deferred Date'));
                //$active_companies = Companies::model()->active()->findAll();
                //$co_ary = array();
                //foreach ($active_companies as $c) {
                //    $co_ary["{$c->company_id}"] = $c->company_name." (".$c->company_id.")";
                //}
                //echo CHtml::activeDropDownList($product,'company_id',$co_ary);
                
                // build the list
                $list = CHtml::listData($product->companies->active_plus_current()->findAll(), 'company_id', 'company_name');
                //print_r($list);
                $current_company_id = $product->companies->company_id;
                $current_co_inactive = ($product->companies->status == Companies::INACTIVE) ? true : false;
                foreach ($list as $key=>$value){
                    
                    if ($current_co_inactive && ($key == $current_company_id)){
                        $list[$key] = $value . ' (inactive)';
                    }
                }
                // display only active companies plus the current one just in case this company is not active
                echo CHtml::activeDropDownList($product,'company_id', $list);

				// build the OS list, lets add say 0 -- not set --
				$os_list = CHtml::listData(Os::model()->findAll(), 'os_id', 'name');
				$os_list[0] = '-- not set --';
								
                ?>
    <?php echo CHtml::error($product,'company_id'); ?> </span></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td class="data_cell"><span class="row">Contact information:
      <?php ?>
  </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'model_number'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'model_number',array('size'=>60,'maxlength'=>64)); ?> <?php echo CHtml::error($product,'model_number'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'sku'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'sku',array('size'=>32,'maxlength'=>32)); ?> <?php echo CHtml::error($product,'sku'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'additional_skus'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextArea($product,'additional_skus',array('rows'=>6, 'cols'=>50)); ?> <?php echo CHtml::error($product,'additional_skus'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'os_id'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'os_id', $os_list); ?> <?php echo CHtml::error($product,'os_id'); ?><span id='os_other_span'><?php echo CHtml::activeLabelEx($product,'os_other'); ?> <?php echo CHtml::activeTextField($product,'os_other',array('size'=>32,'maxlength'=>32)); ?> <?php echo CHtml::error($product,'os_other'); ?> </span></span> </td>
</tr>
<tr>
  <td><span class="row"></td>
  <td class="data_cell"><span class="row"></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'firmware'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'firmware',array('size'=>32,'maxlength'=>32)); ?> <?php echo CHtml::error($product,'firmware'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'wireless_chipset'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'wireless_chipset',array('size'=>60,'maxlength'=>128)); ?> <?php echo CHtml::error($product,'wireless_chipset'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'type_id'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'type_id', CHtml::listData(DeviceTypes::model()->findAll(), 'type_id', 'name')); ?> <?php echo CHtml::error($product,'type_id'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'category_id'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'category_id', CHtml::listData(ProductCategories::model()->findAll(), 'category_id', 'category')); ?> <?php echo CHtml::error($product,'category_id'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'category_other'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'category_other',array('size'=>60,'maxlength'=>128)); ?> <?php echo CHtml::error($product,'category_other'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'is_module'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'is_module', array(0=>'No',1=>'Yes')); ?> <?php echo CHtml::error($product,'is_module'); ?></span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'is_mrcl_recertifiable'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'is_mrcl_recertifiable', array(0=>'No',1=>'Yes')); ?> <?php echo CHtml::error($product,'is_mrcl_recertifiable'); ?></span></td>
</tr>

<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'is_asd'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'is_asd', array(0=>'No',1=>'Yes')); ?> <?php echo CHtml::error($product,'is_asd'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'asd_test_plan'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'asd_test_plan',array('size'=>60,'maxlength'=>128)); ?> <?php echo CHtml::error($product,'asd_test_plan'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'is_dependent'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'is_dependent', array(0=>'No',1=>'Yes')); ?> <?php echo CHtml::error($product,'is_dependent'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'parent_id'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'parent_id',array('size'=>10,'maxlength'=>10)); ?> (0 = no parent product) <?php echo CHtml::error($product,'parent_id'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'product_url'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'product_url',array('size'=>60,'maxlength'=>255)); ?> <?php echo CHtml::error($product,'product_url'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'transfer_source'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'transfer_source'); ?> <?php echo CHtml::error($product,'transfer_source'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'cloned_from'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'cloned_from',array('size'=>10,'maxlength'=>10)); ?> <?php echo CHtml::error($product,'cloned_from'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'obsolete'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'obsolete', array(0=>'No',1=>'Yes')); ?> <?php echo CHtml::error($product,'obsolete'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'product_notes'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextArea($product,'product_notes',array('rows'=>6, 'cols'=>50)); ?> <?php echo CHtml::error($product,'product_notes'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'publish_on'); ?></span></td>
  <td class="data_cell"><span class="row">
    <?php
                    
                    echo CHtml::activeDropDownList($product,'publish_on', $product->publishOnList()); ?>
    <?php echo CHtml::error($product,'publish_on'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'deferred_date'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeTextField($product,'deferred_date',array("id"=>"deferred_date")); ?> &nbsp;(calendar appears when textbox is clicked)
    <?php $this->widget('application.extensions.calendar.SCalendar',
                    array(
                    'inputField'=>'deferred_date',
                    //'ifFormat'=>'%Y-%m-%d',
                    'ifFormat'=>'%m/%d/%Y',
                    'showsTime'=>false
                    ));
                ?>
    <?php echo CHtml::error($product,'deferred_date'); ?> </span></td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'admin_override_display'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'admin_override_display',array(1=>'Yes',0=>'No')); ?> <?php echo CHtml::error($product,'admin_override_display'); ?> </span></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td class="data_cell">    <!-- start supported spatial streams -->
    <table cellpadding="0" cellspacing="1" border="0" class="summary" style="width:auto">
		<tr class="cert_ac_class" >
		  <td>&nbsp;</td>
		  <th colspan="2" style='text-align: center'><b>802.11n</b></th>
		  <th style='text-align: center'>&nbsp;<b>802.11ac</b>&nbsp;</th>
	</tr>
        <tr>
            <th><b>Supported Spatial Streams</b></th>
            <th><b>2.4 GHz</b></th>
            <th><b>5.0 GHz</b></th>
		<th><b>5.0 GHz</b></th>
        </tr>
        <tr>
            <td><b>Transmit (Tx)</b></td>
            <td>
            <?php
                echo CHtml::activeDropDownList($product,'supported_tx_spatial_stream_24', range(0,Products::MAX_SUPPORTED_STREAMS));
            ?>
            </td>
            <td>
            <?php
                echo CHtml::activeDropDownList($product,'supported_tx_spatial_stream_50', range(0,Products::MAX_SUPPORTED_STREAMS));
            ?>
            </td>
	<td>
            <?php
                echo CHtml::activeDropDownList($product,'supported_tx_spatial_stream_50_ac', range(0,Products::MAX_SUPPORTED_STREAMS_AC));
            ?>
            </td>
        </tr>

        <tr class="even">
            <td><b>Receive (Rx)</b></td>
            <td>
            <?php
                echo CHtml::activeDropDownList($product,'supported_rx_spatial_stream_24', range(0,Products::MAX_SUPPORTED_STREAMS));
            ?>

            </td>
            <td>
            <?php
                echo CHtml::activeDropDownList($product,'supported_rx_spatial_stream_50', range(0,Products::MAX_SUPPORTED_STREAMS));
            ?>
            </td>
	<td>
            <?php
                echo CHtml::activeDropDownList($product,'supported_rx_spatial_stream_50_ac', range(0,Products::MAX_SUPPORTED_STREAMS_AC));
            ?>
            </td>
        </tr>
    </table>


</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td class="data_cell">    <!-- start supported spatial streams -->
    <table cellpadding="0" cellspacing="1" border="0" class="summary" style="width:auto">
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
        <td>
          <?php
                echo CHtml::activeDropDownList($product,'certified_tx_spatial_stream_24', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>
          </td>
        <td>
          <?php
                echo CHtml::activeDropDownList($product,'certified_tx_spatial_stream_50', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>  
          </td>
	<td>
          <?php
                echo CHtml::activeDropDownList($product,'certified_tx_spatial_stream_50_ac', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>  
          </td>		  
        </tr>
      
      <tr class="even">
        <td><b>Receive (Rx)</b></td>
        <td>
          <?php
                echo CHtml::activeDropDownList($product,'certified_rx_spatial_stream_24', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>
          
          </td>
        <td>
          <?php
                echo CHtml::activeDropDownList($product,'certified_rx_spatial_stream_50', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>
          </td>
	<td>
          <?php
                echo CHtml::activeDropDownList($product,'certified_rx_spatial_stream_50_ac', range(0,Products::MAX_CERTIFIED_STREAMS));
            ?>
          </td>		  
        </tr>
      </table>
    
  <p class="hint">(Note: changing certified streams will also change the most recent application with a 802.11n certification.)</p>
    
  </td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td class="data_cell"><?php
        // build a single dimension list of cert_id
        //$prod_cert_list = $product->product_certifications; // too slow, use another method
        $prod_cert_list = $product->certificationsArray(); // too slow, use another method
        
        $cert_id_list = array();
        foreach ($prod_cert_list as $row){
            $cert_id_list[] = $row['cert_id'];
        }

    // only show checkboxes if product has publishable applications
        $ca = CertificationApplications::model()
                    ->publishable()
                    ->find('product_id=:product_id', array('product_id'=>$product->product_id));

        // if we cannot find a publishable application, then do not show the cert checkboxes
        if (is_array($ca) && !($ca[0] instanceof CertificationApplications)
        || (!is_array($ca) && !($ca instanceof CertificationApplications))){
            echo "<div style='border: 2px dotted red; padding: 1em; margin: 1em' <b>Changing certifications for a product is not allowed unless at least one application status is \"Completed\", \"Hold\" = 0, the \"Publish On\" field is not set to \"Never\", and the deferred date < today. </b></div>";
        }
        else {
        
            echo $this->renderPartial('_cert_checkboxes', array(
            'certificationsArray'=> $prod_cert_list,
            'is_test_engine'=>(($product->isTestEngine()) ? 1 : 0),
            'cert_id_list'=>$cert_id_list,
            'field_prefix'=>'Products',
            'table_class'=>'product_cert_edit'));
        }
    
    ?></td>
  </tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'external_registrar_support'); ?></span></td>
  <td class="data_cell"><span class="row">
    <?php
            // used for enum type fields
		$schema = $product->getTableSchema()->getColumn('external_registrar_support')->dbType;
		preg_match_all("/'([^']+)'/",$schema,$matches);
		$matches = array_combine($matches[1],$matches[1]);
		echo CHtml::activeDropDownList($product,'external_registrar_support',$matches);
		?>
    <?php echo CHtml::error($product,'external_registrar_support'); ?></span>
    <p class="hint">(Note: product must also have WPS2 certification for External Registrar support to show up.)</p>
    </td>
</tr>
<tr>
  <td><span class="row"><?php echo CHtml::activeLabelEx($product,'internal_registrar_support'); ?></span></td>
  <td class="data_cell"><span class="row"><?php echo CHtml::activeDropDownList($product,'internal_registrar_support',array(0=>'None',1=>'Internal Registrar')); ?> <?php echo CHtml::error($product,'internal_registrar_support'); ?> </span></td>
</tr>
</table>

<div class="wide form">


<!-- edit start -->
        
<!-- edit end-->



    <!-- start displaying related applications -->
    <?php
        //$num_apps = count($apps);
       // for ($i = 0; $i <= $num_apps; $i++){
        foreach($apps as $i=>$app) {
            echo $this->renderPartial('_app_form', array('app'=>$app, 'i'=>$i, 'product'=>$product)); 
        }
    ?>
    <!-- stop displaying related applications -->
<div class="row buttons">
		<?php echo CHtml::submitButton($product->isNewRecord ? 'Create' : 'Save', array('confirm'=>'Save these changes?')); ?>
  </div>
<?php echo CHtml::endForm(); ?>
</div><!-- form -->