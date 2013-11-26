<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
    <div class="row">
		<?php echo $form->labelEx($model,'cid'); ?>
                <?php echo $model->cid; ?>
		<?php echo $form->textField($model,'cid',array('size'=>16,'maxlength'=>16)); ?>
		
	</div>

    <!--
	<div class="row">
		<?php echo $form->label($model,'product_id'); ?>
		<?php echo $form->textField($model,'product_id',array('size'=>10,'maxlength'=>10)); ?>
	</div>
    -->

	<div class="row">
		<?php echo $form->label($model,'company_id'); ?>
		<?php 
                    //echo $form->textField($model,'company_id',array('size'=>10,'maxlength'=>10));
                    $ary = array(''=>'-- choose --');
                    foreach (CHtml::listData(Companies::model()->active()->findAll(), 'company_id', 'company_name') as $key => $value){
                        $ary[$key]=$value;
                    }
                    echo $form->dropDownList($model,'company_id', $ary);
                ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'product_name'); ?>
		<?php echo $form->textField($model,'product_name',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'model_number'); ?>
		<?php echo $form->textField($model,'model_number',array('size'=>60,'maxlength'=>64)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sku'); ?>
		<?php echo $form->textField($model,'sku',array('size'=>32,'maxlength'=>32)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'additional_skus'); ?>
		<?php echo $form->textArea($model,'additional_skus',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'firmware'); ?>
		<?php echo $form->textField($model,'firmware',array('size'=>32,'maxlength'=>32)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'wireless_chipset'); ?>
		<?php echo $form->textField($model,'wireless_chipset',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'type_id'); ?>
		<?php echo $form->textField($model,'type_id',array('size'=>10,'maxlength'=>10)); ?>
	</div>

        <div class="row">
		<?php echo $form->labelEx($model,'category_id'); ?>
                <?php
                    $ary = array(''=>'-- choose --');
                    foreach (CHtml::listData(ProductCategories::model()->findAll(), 'category_id', 'category') as $key => $value) {
                        $ary[$key]=$value;
                    }
                    echo $form->dropDownList($model,'category_id', $ary);

                    ?>
		
	</div>

	<div class="row">
		<?php echo $form->label($model,'category_other'); ?>
		<?php echo $form->textField($model,'category_other',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'is_module'); ?>
                <?php echo $form->dropDownList($model,'is_module', array(''=>'-- choose --', 0=>'No',1=>'Yes')); ?>
		
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'is_mrcl_recertifiable'); ?>
                <?php echo $form->dropDownList($model,'is_mrcl_recertifiable', array(''=>'-- choose --', 0=>'No',1=>'Yes')); ?>
		
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_asd'); ?>
                <?php echo $form->dropDownList($model,'is_asd', array(''=>'-- choose --', 0=>'No',1=>'Yes')); ?>


	</div>

	<div class="row">
		<?php echo $form->label($model,'asd_test_plan'); ?>
		<?php echo $form->textField($model,'asd_test_plan',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'is_dependent'); ?>
                <?php echo $form->dropDownList($model,'is_dependent', array(''=>'-- choose --', 0=>'No',1=>'Yes')); ?>
		
	</div>

	<div class="row">
		<?php echo $form->label($model,'product_url'); ?>
		<?php echo $form->textField($model,'product_url',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'transfer_source'); ?>
		<?php echo $form->textField($model,'transfer_source'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'parent_id'); ?>
		<?php echo $form->textField($model,'parent_id',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'obsolete'); ?>
		<?php echo $form->textField($model,'obsolete'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'product_notes'); ?>
		<?php echo $form->textArea($model,'product_notes',array('rows'=>6, 'cols'=>50)); ?>
	</div>

        <div class="row">
		<?php echo $form->labelEx($model,'publish_on'); ?>
		<!--<?php echo $form->textField($model,'publish_on',array('size'=>32,'maxlength'=>32)); ?>-->
                <?php
                    $ary = array(''=>'-- choose --');
                    foreach ($model->publishOnList() as $key => $value) {
                        $ary[$key]=$value;
                    }
                    echo $form->dropDownList($model,'publish_on', $ary); ?>
		<?php echo $form->error($model,'publish_on'); ?>
	</div>
	<div class="row">

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
		
	</div>

	<div class="row">
		<?php echo $form->label($model,'cloned_from'); ?>
		<?php echo $form->textField($model,'cloned_from',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'admin_override_display'); ?>
		
                <?php echo $form->dropDownList($model,'admin_override_display', array(''=>'-- choose --', 0=>'Yes',1=>'No')); ?>
	</div>
	
		<div class="row">
                <?php echo $form->labelEx($model,'external_registrar_support'); ?>
                <?php
            // used for enum type fields
		$schema = $model->getTableSchema()->getColumn('external_registrar_support')->dbType;
		preg_match_all("/'([^']+)'/",$schema,$matches);
		$matches = array_combine($matches[1],$matches[1]);
                $ary = array(''=>'-- choose --');
                foreach ($matches as $key => $value){
                    $ary[$key]=$value;
                }
		echo $form->dropDownList($model,'external_registrar_support',$ary);
		?>

	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'internal_registrar_support'); ?>
                <?php echo $form->dropDownList($model,'internal_registrar_support',array(''=>'-- choose --', 0=>'None',1=>'Internal Registrar')); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->