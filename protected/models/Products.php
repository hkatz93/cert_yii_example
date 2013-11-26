<?php

/**
 * This is the model class for table "products".
 *
 * The followings are the available columns in table 'products':
 * @property string $product_id
 * @property string $company_id
 * @property string $product_name
 * @property string $model_number
 * @property string $sku
 * @property string $additional_skus
 * @property integer $os_id
 * @property string $os_other
 * @property string $firmware
 * @property string $wireless_chipset
 * @property string $type_id
 * @property string $category_id
 * @property string $category_other
 * @property integer $is_module
 * @property integer $is_mrcl_recertifiable
 * @property integer $is_asd
 * @property string $asd_test_plan
 * @property integer $is_dependent
 * @property string $product_url
 * @property integer $transfer_source
 * @property string $parent_id
 * @property string $cid
 * @property integer $obsolete
 * @property string $product_notes
 * @property string $recert_notes
 * @property string $publish_on
 * @property string $deferred_date
 * @property string $cloned_from
 * @property integer $admin_override_display
 * @property integer $supported_tx_spatial_stream_24
 * @property integer $supported_rx_spatial_stream_24
 * @property integer $supported_tx_spatial_stream_50
 * @property integer $supported_rx_spatial_stream_50
 * @property integer $certified_tx_spatial_stream_24
 * @property integer $certified_rx_spatial_stream_24
 * @property integer $certified_tx_spatial_stream_50
 * @property integer $certified_rx_spatial_stream_50
 * @property integer $certified_tx_spatial_stream_50_ac
 * @property integer $certified_rx_spatial_stream_50_ac
 * @property string $external_registrar_support
 * @property integer $internal_registrar_support
 *
 * The followings are the available model relations:
 * @property array certification_applications CActiveRecord array
 * @property object companies (only one is possible, so not an array of CActiveRecord)
 * @property array product_certifications CActiveRecord array
 *
 */
class Products extends AuditedActiveRecord
{
        /**
         * @var integer max streams allowed by lab testing
         */
        const MAX_CERTIFIED_STREAMS = 3;

        /**
         * @var integer max streams to test requested by end-user
         */
        const MAX_SUPPORTED_STREAMS = 5;
	
      /**
         * @var integer max streams to test requested by end-user
         */
        const MAX_SUPPORTED_STREAMS_AC = 8;

        /**
         * @var string defines one option for publish_on attribute
         */
        const PUBLISH_ON_CERTIFICATION_DATE = 'Certification Date';
        
        /**
         * @var string defines one option for publish_on attribute
         */
        const PUBLISH_ON_NEVER = 'Never';

        /**
         * @var string defines one option for publish_on attribute
         */
        const PUBLISH_ON_DEFERRED_DATE = 'Deferred Date';

        
	/**
	 * Returns the static model of the specified AR class.
	 * @return Products the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'products';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		// If you do NOT define the rule for the attribute, it will not be set properly from a form that uses mass attribute assignation
		return array(
			array('product_name', 'required'),

			array('company_id, parent_id, is_module, is_mrcl_recertifiable, is_asd, is_dependent, transfer_source, type_id, category_id, obsolete, os_id, admin_override_display, supported_tx_spatial_stream_24, supported_rx_spatial_stream_24, supported_tx_spatial_stream_50, supported_rx_spatial_stream_50, certified_tx_spatial_stream_24, certified_rx_spatial_stream_24, certified_tx_spatial_stream_50, certified_rx_spatial_stream_50_ac, certified_tx_spatial_stream_50_ac, certified_rx_spatial_stream_50, internal_registrar_support, cloned_from', 'numerical', 'integerOnly'=>true),
			array('certified_tx_spatial_stream_24, certified_rx_spatial_stream_24, certified_tx_spatial_stream_50, certified_rx_spatial_stream_50', 'in', 'range'=>array(0,1,2,3)),
			array('supported_tx_spatial_stream_50_ac, supported_rx_spatial_stream_50_ac, certified_tx_spatial_stream_50_ac, certified_rx_spatial_stream_50_ac', 'in', 'range'=>array(0,1,2,3,4,5,6,7,8)),
                        array('product_name, wireless_chipset, category_other, asd_test_plan', 'length', 'max'=>128),
			array('model_number', 'length', 'max'=>64),
			array('sku, firmware, publish_on', 'length', 'max'=>32),
			array('product_url', 'length', 'max'=>255),
			//array('cid', 'length', 'max'=>16), // not changeable if not in rules, implicit "unsafe" (not able to change)
			array('external_registrar_support', 'length', 'max'=>47),
			array('additional_skus, product_notes, deferred_date, os_other', 'safe'),
                        array('parent_id', 'validateParentId'),
                        array('cloned_from', 'validateClonedFrom'),
                        array('os_other', 'validateOsOther'),
                        array('publish_on', 'in', 'range'=>$this->publishOnList(), 'message'=>'publish on must be one of the following: '.(implode(',', $this->publishOnList()))),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
                        // note: the 'on'=>'search' makes these "safe" ONLY for search, so these rules will be ignored in other contexts
			array('product_id, company_id, product_name, model_number, sku, additional_skus, firmware, wireless_chipset, 
				type_id, category_id, os_id, os_other, category_other, is_module, is_mrcl_recertifiable, is_asd, 
				asd_test_plan, is_dependent, product_url, transfer_source, parent_id, cid, obsolete,
				product_notes, publish_on, deferred_date, cloned_from, admin_override_display, 
				supported_tx_spatial_stream_24, supported_rx_spatial_stream_24, 
				supported_tx_spatial_stream_50, supported_rx_spatial_stream_50, 
				certified_tx_spatial_stream_24, certified_rx_spatial_stream_24, 
				certified_tx_spatial_stream_50, certified_rx_spatial_stream_50, 
				certified_tx_spatial_stream_50_ac, certified_rx_spatial_stream_50_ac,
				supported_tx_spatial_stream_50_ac, supported_rx_spatial_stream_50_ac,
				external_registrar_support, internal_registrar_support', 'safe', 'on'=>'search'),
		);
	}

        public function validateParentId($attribute, $params){
            if ($this->parent_id == 0){
                return true;
            }
            if ($this->parent_id == $this->product_id){
                $this->addError('parent_id', 'cannot have same parent_id as this product! definately illegal in some states');
            }
            $p = $this->findByPk($this->parent_id);
            if (!($p instanceof Products)){
                $this->addError('parent_id', 'no matching product found for parent_id');
            }
            else {
               // print "product parent found (parent_id = {$p->product_id}";
            }
        }

        public function validateClonedFrom($attribute, $params){
            if ($this->cloned_from == 0){
                return true;
            }
            if ($this->cloned_from == $this->product_id){
                $this->addError('cloned_from', 'cannot have same cloned from as this product!');
            }
            $p = $this->findByPk($this->cloned_from);
            if (!($p instanceof Products)){
                $this->addError('cloned_from', 'no matching product found for cloned from');
            }
            else {
               // print "product parent found (parent_id = {$p->product_id}";
            }
        }

		public function validateOsOther() {
				$os_other = trim($this->os_other);
				//print ("os  = " . $this->os->name );
				if ($this->os->name == 'Other' && empty($os_other)) {
						 $this->addError('os_other', 'Other operating system cannot be blank when"Other" selected');
				}
		}
	/**
	 * @return array relational rules.
         * NOTE: the product_certifications relation is VERY slow, consider using certificationArray() instead
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'certification_applications' => array(self::HAS_MANY, 'CertificationApplications', 'product_id'),
                    'companies' => array(self::BELONGS_TO, 'Companies', 'company_id'),
				  'os'=>array(self::BELONGS_TO, 'Os', 'os_id'),                    
				  //'product_certifications' => array(self::MANY_MANY, 'ProductCertifications', 'app_id'),

                    //'TPosts'=>array(self::HAS_MANY, 'TPosts', '', 'on'=>'tu_uid=uid', 'joinType'=>'INNER JOIN', 'alias'=>'TPosts')

                    // product_certifications relation VERY slow with many rows of data (as production currently has)
                    // borderline dangerous to use (resource hog), so removing it
                    //'product_certifications'=>array(self::HAS_MANY, 'ProductCertifications', '', 'on'=>'cid=cid', 'joinType'=>'INNER JOIN', 'alias'=>'product_certifications'),
                    'product_categories'=>array(self::BELONGS_TO, 'ProductCategories', 'category_id'),
                    'device_types'=>array(self::BELONGS_TO, 'DeviceTypes', 'type_id'),
                    
                    //'requested_certifications' => array(self::MANY_MANY, 'RequestedCertifications', 'certification_applications(product_id, app_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
            {
		return array(
			'product_id' => 'Product ID',
			'company_id' => 'Company',
			'product_name' => 'Product Name',
			'model_number' => 'Model Number',
			'sku' => 'SKU#',
			'additional_skus' => 'Additional SKU#s',
			'os_id' => 'Operating System',
			'os_other' => 'Other Operating System ',
			'firmware' => 'Firmware',
			'wireless_chipset' => 'Wireless Chipset',
			'type_id' => 'Device Type',
			'category_id' => 'Primary Category',
			'category_other' => 'Category Other',
			'is_module' => 'Is Module',
			'is_mrcl_recertifiable' => 'Is MRCL Re-certifiable',
			'is_asd' => 'Is Asd',
			'asd_test_plan' => 'Asd Test Plan',
			'is_dependent' => 'Is Dependent',
			'product_url' => 'Product Url',
			'transfer_source' => 'Transfer Source',
			'parent_id' => 'Parent Product Id',
			'cid' => 'CID',
			'obsolete' => 'Obsolete',
			'product_notes' => 'Product Notes',
			'recert_notes' => 'Re-certification Notes',
			'publish_on' => 'Publish On',
			'deferred_date' => 'Deferred Date',
			'cloned_from' => 'Cloned From (Product ID)',
			'admin_override_display' => 'Display Publicly?',
			'supported_tx_spatial_stream_24' => 'Supported Tx Spatial Stream 2.4 GHz',
			'supported_rx_spatial_stream_24' => 'Supported Rx Spatial Stream 2.4 GHz',
			'supported_tx_spatial_stream_50' => 'Supported Tx Spatial Stream 5.0 GHz',
			'supported_rx_spatial_stream_50' => 'Supported Rx Spatial Stream 5.0 GHz',
			'certified_tx_spatial_stream_24' => 'Certified Tx Spatial Stream 2.4 GHz',
			'certified_rx_spatial_stream_24' => 'Certified Rx Spatial Stream 2.4 GHz',
			'certified_tx_spatial_stream_50' => 'Certified Tx Spatial Stream 5.0 GHz',
			'certified_rx_spatial_stream_50' => 'Certified Rx Spatial Stream 5.0 GHz',
			'external_registrar_support' => 'External Registrar Support',
			'internal_registrar_support' => 'Internal Registrar Support',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('company_id',$this->company_id);
		$criteria->compare('product_name',$this->product_name,true);
		$criteria->compare('model_number',$this->model_number,true);
		$criteria->compare('sku',$this->sku,true);
		$criteria->compare('additional_skus',$this->additional_skus,true);
		$criteria->compare('os_id',$this->os_id);
		$criteria->compare('os_name',$this->os_id, true);
		$criteria->compare('firmware',$this->firmware,true);
		$criteria->compare('wireless_chipset',$this->wireless_chipset,true);
		$criteria->compare('type_id',$this->type_id,true);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('category_other',$this->category_other);
		$criteria->compare('is_module',$this->is_module);
		$criteria->compare('is_mrcl_recertifiable',$this->is_mrcl_recertifiable);
		$criteria->compare('is_asd',$this->is_asd);
		$criteria->compare('asd_test_plan',$this->asd_test_plan,true);
		$criteria->compare('is_dependent',$this->is_dependent);
		$criteria->compare('product_url',$this->product_url,true);
		$criteria->compare('transfer_source',$this->transfer_source);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('cid',$this->cid,true);
		//$criteria->compare('obsolete',$this->obsolete);
		$criteria->compare('product_notes',$this->product_notes,true);
		$criteria->compare('publish_on',$this->publish_on,true);
		$criteria->compare('deferred_date',$this->deferred_date,true);
		$criteria->compare('cloned_from',$this->cloned_from,true);
		$criteria->compare('admin_override_display',$this->admin_override_display);
		$criteria->compare('supported_tx_spatial_stream_24',$this->supported_tx_spatial_stream_24);
		$criteria->compare('supported_rx_spatial_stream_24',$this->supported_rx_spatial_stream_24);
		$criteria->compare('supported_tx_spatial_stream_50',$this->supported_tx_spatial_stream_50);
		$criteria->compare('supported_rx_spatial_stream_50',$this->supported_rx_spatial_stream_50);
		$criteria->compare('certified_tx_spatial_stream_24',$this->certified_tx_spatial_stream_24);
		$criteria->compare('certified_rx_spatial_stream_24',$this->certified_rx_spatial_stream_24);
		$criteria->compare('certified_tx_spatial_stream_50',$this->certified_tx_spatial_stream_50);
		$criteria->compare('certified_rx_spatial_stream_50',$this->certified_rx_spatial_stream_50);
		$criteria->compare('certified_tx_spatial_stream_50_ac',$this->certified_tx_spatial_stream_50_ac);
		$criteria->compare('certified_rx_spatial_stream_50_ac',$this->certified_rx_spatial_stream_50_ac);
		$criteria->compare('external_registrar_support',$this->external_registrar_support,true);
		$criteria->compare('internal_registrar_support',$this->internal_registrar_support);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}


        /**
         * change dates back to database friendly format
         */
        private function _beforeSaveConvertDates() {
            $this->deferred_date = CertificationApplications::datestr2YYYYMMDD($this->deferred_date, false);
        }
	private function _beforeSaveOs() {
			if ($this->os->name != 'Other') {
					$this->os_other = '';
			}
	}
        /**
         * change dates to MM-DD-YYYY format
         */
        private function _afterFindConvertDates(){
            $this->deferred_date = CertificationApplications::time2MMDDYYYY(strtotime($this->deferred_date), false);
        }
        /**
         * Deletes all certifications from product that are listed in this application.
         * This could unintentionally delete certifications if certifications are duplicated
         * between applications of this product, so a warning is needed.
         *
         * @todo update activity_log table with the transaction details
         */
        public function beforeDelete() {
       
            parent::beforeDelete();
            
            // assure we cannot delete the product if it has dependent products
            $ary = $this->getDependentProducts();
            //print __METHOD__ .'ary=';print_r($ary);
            if (!empty($ary)){
                $this->addError('product_id', 'cannot delete a parent product');
                return false;
            }
            
            
            //$row = $model->getDbConnection()->createCommand('SHOW TABLE STATUS LIKE TableName')->queryRow();

            $connection=Yii::app()->db;   // assuming you have configured a "db" connection
            //print_r($connection);
            $product_id = $this->product_id;
            Yii::log("[product_id = $product_id ]");
            $sql = " DELETE ca, rc, tr, td, pc"
                    ." FROM certification_applications as ca"
                    ." INNER JOIN requested_certifications as rc ON (ca.app_id = rc.app_id)"
                    ." LEFT JOIN test_results as tr ON (tr.request_id = rc.request_id)"
                    ." LEFT JOIN test_data as td ON (td.request_id = tr.request_id)"
                    ." INNER JOIN products as p ON (p.product_id = ca.product_id)"
                    ." LEFT JOIN product_certifications as pc ON (p.cid = pc.cid and pc.cert_id = rc.cert_id)"
                    ." WHERE ca.product_id=$product_id";
                    //." WHERE ca.product_id=:product_id";

            $command=$connection->createCommand($sql);
            //$command->bindParam(":product_id",$model->product_id,PDO::PARAM_INT);
            //$command->bindParam(":cert_id",$username,PDO::PARAM_INT);
            $rv = $command->execute();

            if ($rv){
                Yii::log("[deleted $rv rows]");
                return true;
            }
            else {
                Yii::log("problem with beforeDelete for product model");
                return false; // this will halt the deletion
            }
        }

        /**
         *
         * @return boolean if not true then all saves are prevented
         */
        public function beforeSave() {
            parent::beforeSave();
            $this->_beforeSaveConvertDates();
            $this->_beforeSaveOs();
            return true;
        }

        public function afterFind(){
            parent::afterFind();
            $this->_afterFindConvertDates();
        }
        
        /**
         * returns a cid from a product id
         *
         * @param integer $product_id
         * @return string
         */
        public function productId2Cid($product_id){
            return "WFA".(str_pad($product_id, 4, '0', STR_PAD_LEFT));
            
        }

        /**
         * converts a cid to product_id
         * 
         * @param string $cid
         * @return integer
         */
        public function cid2ProductId($cid){
            if (preg_match("/^WFA\d+/", $cid)) {
                $str = str_replace('WFA','', $cid);
            }
            elseif (preg_match("/^W\d+/", $cid)) {
                $str = str_replace('W','', $cid);
            }
            else {
                throw new InvalidArgumentException("not a cid format");
            }
            $rv = (int)$str;
            if (empty($rv)) {
                return null;
            }
            else {
                return (int)$str;
            }
        }

        /**
         * Adds a certification to the product level (does not touch applications)
         * 
         * @param integer $cert_id
         * @param boolean $test_result TestResults::(PASS|FAIL|NOT_TESTED) (currently non-functional since this is app based)
         * @return boolean true if success
         */
        public function addCertification($cert_id, $test_result=TestResults::PASS){

            // prevent adding duplicate certifications
            //$existing_pc = ProductCertifications::find('cert_id=:cert_id AND cid=:cid', array('cert_id'=>$cert_id, 'cid'=>$this->cid));

            // add new ProductCertification

            $pc = new ProductCertifications();
            $tmp = array();
            //$tmp['cid'] = $this->productId2Cid($this->product_id);
            $tmp['cid'] = $this->cid;
            $tmp['cert_id'] = $cert_id;
            $pc->setAttributes($tmp);
            $rv = $pc->save();
            //print __FUNCTION__; print "::"; var_dump($rv);
            if ($rv == false){
                $errors = $pc->errors;
                //print "pc_errors = "; print_r($errors);
                $this->addError('cert_id', $errors['cert_id'][0]);
                //print "errors = ";
                //print_r($pc->errors);
                
            }
            return $rv;
        }

        /**
         * Remove a certification from the product level (does not affect applications)
         *
         * @param integer $cert_id
         * @return boolean true if successful
         */
        public function deleteCertification($cert_id){
            $pc = ProductCertifications::model()->find('cid=:cid AND cert_id=:cert_id', array('cid'=>$this->cid,'cert_id'=>$cert_id));
            $rv = $pc->delete();
            if ($rv == false){
                $this->addError('product_id', "unable to delete certification (cert_id = $cert_id)");
            }
            return $rv;
        }

        public function setCertifications($cert_id_list){
            // get the current certifications

            // determine which ones should be deleted and delete them
            // determine which ones should be added and add them
            $cert_ary = $this->certificationsArray();
            $existing_cert_id_list = array();

            $cert_ary = $this->certificationsArray();
            $existing_cert_id_list = array();
            foreach ($cert_ary as $row){
                $existing_cert_id_list[]=$row['cert_id'];
            }

            // prevent bug: assure each variable is an array
            $existing_cert_id_list = (is_array($existing_cert_id_list)) ? $existing_cert_id_list : array();
            $cert_id_list = (is_array($cert_id_list)) ? $cert_id_list : array();
            
            $delete_list = array_diff($existing_cert_id_list, $cert_id_list);
            $add_list = array_diff($cert_id_list, $existing_cert_id_list);

            $has_error = false;

            // important to add the new ones first
            // for cert_n we cannot delete if cert_b does not exist
            foreach ($add_list as $cert_id){
                $rv = $this->addCertification($cert_id);
                if (!$rv){
                    $this->addError('cert_id', "unable to add certification cert_id=$cert_id");
                    //print "unable to add cert_id=$cert_id, ";
                }
                $has_error = ($rv == false) ? true : $has_error;
            }

            foreach ($delete_list as $cert_id){
                $rv = $this->deleteCertification($cert_id);
                if (!$rv){
                    $this->addError('cert_id', "unable to delete certification cert_id=$cert_id");
                    //print "unable to delete cert_id=$cert_id, ";
                }
                $has_error = ($rv == false) ? true : $has_error;
            }
            
            
            if ($has_error){
                return false;
            }
            else {
                return true;
            }
        }

        /**
         * grab the active record for the most recently published certification
         * that contains the N certification
         * 
         * @param integer $product_id (optional, will base on current product if set)
         * @return CertificationApplications
         */
        public function findLatestAppWithCertN($product_id=null){
            $product_id = (isset($product_id)) ? $product_id : $this->product_id;

            if (empty($product_id)){
                throw new Exception("cannot lookup app with blank product_id");
            }
            $cert_n_list_str = implode(',', Certifications::getCertNRequiredList());
            //print "product_id={$product_id} and cert_id in ($cert_n_list_str)";
            $ca = CertificationApplications::model()
                ->publishable()
                //->most_recent() (appearently adding this named scopes causes sql error)
                ->with('requested_certifications')
                ->find("product_id={$product_id} and cert_id in ($cert_n_list_str)"); // grab top of sorted list starting with most recently certified
                //->find("product_id=:product_id and cert in ($cert_n_list_str)", array('product_id', $product_id)); // error with bound parameters

          
            return $ca;
        }

        public function afterSave() {
            parent::afterSave();

            // go back and update the application with changed stream results
            $app = $this->findLatestAppWithCertN();
            if ($app instanceof CertificationApplications){
                $app->certified_tx_spatial_stream_24 = $this->certified_tx_spatial_stream_24;
                $app->certified_rx_spatial_stream_24 = $this->certified_rx_spatial_stream_24;
                $app->certified_tx_spatial_stream_50 = $this->certified_tx_spatial_stream_50;
                $app->certified_rx_spatial_stream_50 = $this->certified_rx_spatial_stream_50;
                $rv = $app->save();
                if (!$rv){
                    Yii::log("unable to update application (app_id = {$app->app_id}) with stream results");
                }
            }
            
        }


        /**
         * options possible for publish_on attribute
         * @return array
        */
        public function publishOnList() {
            return array(
                self::PUBLISH_ON_CERTIFICATION_DATE => self::PUBLISH_ON_CERTIFICATION_DATE,
                self::PUBLISH_ON_DEFERRED_DATE => self::PUBLISH_ON_DEFERRED_DATE,
                
                self::PUBLISH_ON_NEVER => self::PUBLISH_ON_NEVER,
            );
        }

        /**
         * we want all of the published the certification list sorted in a special way
         * seems easiest to use SQL rather than a complex active record
         *
         * @return array from queryAll()
         */
        public function certificationsArray() {
            $sql = "/* publishedCertificationsArray */ SELECT cc.category, c.cert_id, c.display_name, pc.product_certification_id"
                    . " FROM products as p "
                    . " INNER JOIN product_certifications as pc ON (pc.cid = p.cid)"
                    . " INNER JOIN certifications as c ON (c.cert_id = pc.cert_id)"
                    . " INNER JOIN certification_categories as cc ON (cc.category_id = c.category_id)"
                    . " WHERE p.product_id=:product_id"
                    . " ORDER BY cc.placement, c.placement";


            $connection=Yii::app()->db;   // assuming you have configured a "db" connection
            $command=$connection->createCommand($sql);
	   $product_id = $this->product_id;
            $command->bindParam(":product_id",$product_id,PDO::PARAM_INT);

            return $command->queryAll();
        }

        /**
         * helper function, makes certificationArray one-dimensional
         * @uses Products::certificationsArray()
         * @return array
         */
        public function certIdList() {
            $rows = $this->certificationsArray();
            $cert_id_list = array();
            foreach ($rows as $row){
                $cert_id_list[]=$row['cert_id'];
            }
            return $cert_id_list;
        }
        /**
         * NOTICE: this is a hack to bypass problem with real data making the
         * standard relations() for "product_certifications" too slow for product_certifications.
         * 
         * @return mixed either array of ProductCertifications objects or single object
         */
        public function getProductCertifications() {
            $cert_rows = $this->certificationsArray();
            $out_ary = array();
            
            foreach ($cert_rows as $row) {
                $out_ary[] = ProductCertifications::model()->findByPk($row['product_certification_id']);
            }
            
            if (count($out_ary) > 1){
                return $out_ary;
            }
            elseif (count($out_ary) == 1){
                return $out_ary[0];
            }
            else {
                return false;
            }
        }
        
        /**
         * determines whether a product is a test_engine based on its application(s)
         * @return boolean
         */
        public function isTestEngine(){
            $is_test_engine = false;
            $ca = CertificationApplications::model()->find("product_id=:product_id", 
                    array('product_id'=>$this->product_id));
            if (!is_array($ca)){
                if ($ca instanceof CertificationApplications){
                    $is_test_engine = ($ca->test_engine) ? true : false;
                }
            }
            else {
                // loop over all existing applications and find the test_engine status
                foreach ($ca as $ca_obj){
                    $is_test_engine = ($ca->test_engine) ? true : $is_test_engine;
                }
            }
            return $is_test_engine;
        }
        
        /**
         * determines whether a product is a Access Point product based on its category
         * @return boolean
         * @todo also include the criteria if reference design then the 1st subcategory determines type AP/STA
         */
        public function isAccessPoint(){
            if (in_array($this->category_id, array(
                ProductCategories::AP_HOME,
                ProductCategories::AP_ENTERPRISE,
                ProductCategories::AP_GATEWAY,
                ProductCategories::AP_MOBILE,
                
            ))) {
                return true;
            }
            else {
                return false;
            }
        }
        
        /**
         * determines whether a product is a Station product based on its category
         * @return boolean
         */
        public function isStation(){
            if ($this->isAccessPoint()) {
                return false ;
            }
            else {
                 return true;
            }
        }
        
        /**
         * get the parent product (module) if it exists
         * @return object AuditedActiveRecord
         */
        public function getParentProduct() {
            $parent_prod = Products::model()->find('product_id = :parent_id', array('parent_id'=>$this->parent_id));
            return $parent_prod;
        }
        
        /**
         * returns dependent products
         * @return array of Products objects, can be empty 
         */
        public function getDependentProducts(){
            // NOTE: we are required to join with certification_applications 
            // since it is possible for rows in the products table to have no associated application 
            // if the application was deleted before the lab accepted the application
            //
            $dep_prod_list = Products::model()
                    ->with('certification_applications')
                    ->findAll('parent_id=:product_id AND app_id IS NOT NULL', array('product_id'=>$this->product_id));
            
            if (!is_array($dep_prod_list)) {
                return array($dep_prod_list);
            }
            else {
                return $dep_prod_list;
            }
        }
        
        /**
         * return the most recent completed requested certification.
         * used for copying parent product certs
         * 
         * @param type $cert_id 
         * @return object RequestedCertifications
         */
        public function getMostRecentPublishableRequestedCertificationByCertId($cert_id) {
            $sql = "/* MOST RECENT PUBLISHABLE REQUESTED CERT BY PRODUCT_ID AND CERT_ID */ "
                . " SELECT rc.request_id "
                ." FROM certification_applications as ca "
                ." INNER JOIN requested_certifications as rc ON (ca.app_id = rc.app_id) "
                ." WHERE "
                ." ca.status = ".CertificationApplications::STATUS_COMPLETE
                ." AND ca.deferred_date <= NOW() "
                ." AND ca.publish_on !='Never' "
                ." AND ca.hold = 0 "
                ." AND ca.product_id = :product_id"
                ." AND rc.cert_id = :cert_id "
                ." ORDER BY ca.app_id DESC"
                ." LIMIT 1";
                
               // . " LIMIT :limit";

            $connection = Yii::app()->db;   // assuming you have configured a "db" connection
            $command = $connection->createCommand($sql);
	   $product_id = $this->product_id;
            $command->bindParam(":product_id", $product_id, PDO::PARAM_INT);
            $command->bindParam(":cert_id", $cert_id ,PDO::PARAM_INT);
            $rows = $command->queryAll();
     
            return RequestedCertifications::model()->findByPk($rows[0]['request_id']);
        }
 
        /**
         * imports a publishable application from a parent to a dependent by appending
         * the certifications and results to the last publishable application of
         * the dependent product.
         * 
         * @return boolean 
         */
        public function appendParentApplicationResults($app_id) {
            $DEBUG = 0;
            $connection=Yii::app()->db;   
             
            // first confirm that app_id belongs to the parent
            $parent_prod = $this->getParentProduct();
            if (!($parent_prod instanceof Products)){
                $this->addError('product_id', 'Could not copy parent application. Parent does not exist');
                return false;
            }

            // grab parent app
            $parent_app = CertificationApplications::model()->findByPk($app_id);
            
            // then validate that the parent app_id is publishable
            if (!$parent_app->isPublishable()) {
                $this->addError('product_id', 'Parent application is not publishable. Importing is not allowed.');
                return false;
            }
            
            if (!($parent_app instanceof CertificationApplications)) {
                if ($DEBUG){ 
                    print "--->parent_app = ".print_r($parent_app); print "\n\n";
                }
                $this->addError('product_id', 'unable to load parent application');
                return false;
            }
            
            // find the last publishable application of dependent product
            //
            $dep_app = CertificationApplications::model()
                    //->publishable() // don't use since we will want to add to apps that are not published yet
                    ->completed()
                    ->find('product_id=:product_id', array('product_id'=>$this->product_id));
            
            if (!($dep_app instanceof CertificationApplications)){
                $this->addError('product_id',"Unable to load a publishable application for this product (product_id={$this->product_id}). Is it publishable?");
                return false;
            }
  
            // find  the requested certifications from parent
            $parent_rc_list = RequestedCertifications::model()->findAll('app_id=:app_id', array('app_id'=>$parent_app->app_id));
            if (empty($parent_rc_list)){
                $this->addError('product_id', 'unable to find parent product requested certifications for app');
                return false;
            }
            if (!($parent_rc_list[0] instanceof RequestedCertifications)){
                $this->addError('product_id', 'unable to find parent product requested certifications for app');
                return false;
            }
            
            
            // copy the parent requested certifications to the dependent
            // don't copy them over if there are duplicate cert_ids
            // in the publishable dependent certification application
            //
            $existing_cert_id_list = $dep_app->certIdList();
            foreach($parent_rc_list as $parent_rc) {
                
                // do not import CWG-RF
                if ($parent_rc->cert_id == Certifications::CERT_CWG_RF){
                    continue;
                }
                
                // move to next requested certification if cert_id already exists
                // for the dependent product
                if (in_array($parent_rc->cert_id, $existing_cert_id_list)){
                    continue;
                }
                
                // create the dependent requested certification
                // copy the contents from the parent
                //
                $dep_rc = new RequestedCertifications();
                // copy all of parent fields over
                $rv = $dep_rc->setAttributes($parent_rc->getAttributes());
                // set the app_id of the dependent app 
                $dep_rc->app_id = $dep_app->app_id;
                if (!$dep_rc->save()){
                    $this->addError('product_id', "Unable to save dependent requested certification");
                    return false;
                }
                
                if ($DEBUG){
                    print "saved: dep_rc->request_id = {$dep_rc->request_id}\n";
                }
                
                // copy over the external data, if it exists
                if ($dep_rc->shouldHaveExternalDataFile()) {
                    print "copying over external data for cert_id={$dep_rc->cert_id}\n";
                    $rv = $dep_rc->copyExternalData($parent_rc);
                }
                
                // save test results from parent
                $parent_tr = $parent_rc->test_results;
                $dep_tr = new TestResults();
                $dep_tr->setAttributes($parent_tr->getAttributes());
                $dep_tr->request_id = $dep_rc->request_id;
                if (!$dep_tr->save()) {
                    $this->addError('product_id', "Unable to inherit parent test result for request_id={$dep_tr->request_id}");
                    return false;
                }
                if ($DEBUG){
                    print "saved: dep_tr->request_id = {$dep_tr->request_id}\n";
                }
                
                // add the published certification to dependent product
                $pc = new ProductCertifications();
                $pc->cid = $this->productId2Cid($this->product_id);
                $pc->cert_id = $parent_rc->cert_id;
                if (!$pc->save()){
                    // get the errors 
                    $pc_errors = print_r($pc->errors, true);
                    
                    $this->addError('product_id', "Unable to save published certification: pc_errors = $pc_errors");
                    return false;
                }
                
                // copy over the lab data, if it exists
                $sql = "/* COPY TEST DATA FROM PARENT */"
                 . " INSERT into test_data (request_id, field_id, data, posted_on, posted_by) "
                 . " SELECT :dep_request_id as request_id, field_id, data, posted_on, posted_by "
                 . " FROM test_data WHERE request_id=:parent_request_id";
                                
                $command=$connection->createCommand($sql);
                $command->bindParam(":dep_request_id",$dep_rc->request_id,PDO::PARAM_INT);
                $command->bindParam(":parent_request_id",$parent_rc->request_id,PDO::PARAM_INT);
                $command->execute();
            }
            
            return true;
        }
		
	/**
	 *  import an application from module to dependent product. CWG-RF will NOT be copied over, nor with this automatically
	 * publish the certifications to production
	 * 
	 * @param integer $app_id parent app_id
	 * @return boolean
	 */
	public function importParentApplication($app_id) {
		$parent_ca = CertificationApplications::model()->findByPk($app_id);
		if (!($parent_ca instanceof CertificationApplications)) {
			$this->addError('app_id', "unable to find app_id of parent application (app_id=$app_id)");
			return false;
		}
		// only allow certain types of applications to be exported (re-cert, additional)
		if (!in_array($parent_ca->certification_type, array(CertificationApplications::TYPE_ADDITIONAL, CertificationApplications::TYPE_RECERT))) {
			$this->addError('certification_type', "only Additional or Re-certification applications are allowed to be imported");
			return false;
		}
		//if (!$ca->)
		$parent_prod = Products::model()->findByPk($parent_ca->product_id);
		if (empty($parent_prod->is_module)) {
			$this->addError('product_id', "parent product (product_id = {$parent_prod->product_id} is not a module, cannot import application");
			return false;
		}
		
		
		$dep_prod_id = $this->product_id;
		$parent_prod_id = $this->parent_id;;
		$dep_prod = Products::model()->findByPk($dep_prod_id);
		$parent_prod = Products::model()->findByPk($parent_prod_id);
		$dep_prod->os_id = $parent_prod->os_id;
		$dep_prod->os_other = $parent_prod->os_other;
		$dep_prod->firmware = $parent_prod->firmware;
		$dep_prod->wireless_chipset = $parent_prod->wireless_chipset;
		$rv = $dep_prod->save();
		if (0) {
				unset ($dep_prod);
				$dep_prod = Products::model()->findByPk($dep_prod_id);
				print "rv = " . (print_r($rv, true)) . "\n";
				$dep_prod = Products::model()->findByPk($dep_prod_id);
				$attr = $dep_prod->getAttributes();
				print "dep_attributes" . (print_r($attr, true));
				$attr = $parent_prod->getAttributes();
				print "parent_attributes" . (print_r($attr, true));
		}
		// see if this application has already been imported into this product
		$previously_imported_app = CertificationApplications::model()->find('product_id=:product_id AND import_parent_app_id=:import_parent_app_id',
				array ('product_id'=>$dep_prod_id, 'import_parent_app_id'=>$app_id));
		if ($previously_imported_app instanceof CertificationApplications) {
			$this->addError('import_parent_app_id', "cannot import application a second time (parent app_id=$app_id");
			return false;
		}
		// copy application
		$new_app_attr = $parent_ca->attributes;
		
		unset ($new_app_attr['app_id']); // make sure to remove the app_id so it can be generated based on incremental count
		$new_app_attr['product_id'] = $dep_prod_id; // link to dependent product
		$new_app_attr['import_parent_app_id'] = $app_id; // link to dependent product
		//print "new_app_attr\n========="; print_r($new_app_attr); print "\n======\n";
		
		$dep_ca = new CertificationApplications();
		$dep_ca->setAttributes($new_app_attr);
		$rv = $dep_ca->save();
		if (!$rv) {
			$this->addError('import_parent_app_id', 'unable to import app_id=$app_id : '. print_r($dep_ca->getErrors(), true));
			return false;
		}
		// get the dependent app_id
		//$dep_ca = CertificationApplications::model()->find()
		//$dep_ca = CertificationApplications::model()->find('import_parent_app_id=:import_parent_app_id', 
		//	array('import_parent_app_id'=>$app_id));
		
		$dep_app_id = $dep_ca->getPrimaryKey();
		
		// copy over parent requested certifications
		foreach ($parent_ca->requested_certifications as $p_rc) {
			// skip over if CWG-RF, dependents are not assumed to get this imported; it must be done again for dep prod
			if ($p_rc->cert_id == Certifications::CERT_CWG_RF){
				continue;
			}
			$attr = $p_rc->attributes;
			unset($attr['request_id']);
			$attr['app_id'] = $dep_app_id;
			//$attr['app_id'] = $dep_ca->app_id;
			$tmp_rc = new RequestedCertifications();
			$tmp_rc->setAttributes($attr);
			$rv = $tmp_rc->save();
			if (!$rv) {
				$this->addError('request', "not able to save requested certifications : ".(print_r($tmp_rc->getErrors(), true)));
				return false;
			}
			// copy over external data	
			$tmp_rc->copyExternalData($p_rc);
					
			$dep_request_id = $tmp_rc->getPrimaryKey();
			//print "dep request id = $deP-request_id \n";
			
			// copy over test results	
			$tmp_tr = new TestResults();
			$p_tr = $p_rc->test_results;
			$attr = $p_tr->attributes;
			if (empty($p_tr)) {
				//print "parent test result attr = ".print_r($attr,true);
				$this->addError('result_id', "empty test result (request_id=$dep_request_id): ".(print_r($tmp_tr->getErrors(), true)));
				return false;
			}
			
			unset($attr['result_id']);
			$attr['request_id'] = $dep_request_id;
			$attr['import_result_id'] = $p_tr->result_id;
			$tmp_tr->setAttributes($attr);
			$rv = $tmp_tr->save();
			if (!$rv) {
				//print "attr = ".print_r($attr,true);
				$this->addError('result_id', "not able to save test result (request_id=$dep_request_id): ".(print_r($tmp_tr->getErrors(), true)));
				return false;
			}
			
			// copy over test_data
			$p_td_list = $p_rc->test_data;
			if (is_array($p_td_list)){	
				foreach ($p_td_list as $p_td) {
					$tmp_td = new TestData();
					$attr = $p_td->attributes;
					unset($attr['data_id']);
					$attr['request_id'] = $dep_request_id;
					$tmp_td->setAttributes($attr);
					$rv = $tmp_td->save();
					if (!$rv) {
						$this->addError('data_id', "not able to save test data (request_id=$dep_request_id): attr=".(print_r($attr, true)) . ", getErrors = " . (print_r($tmp_td->getErrors(), true)));
						return false;
					}
				}
			} 
		}
		// if parent_app is publishable, push the dependent app certs live if they have a passing result
		// add the published certification to dependent product
		// make sure not to publish a cert if it already exists, this will trip a validation error
		$existing_certs = $this->certIdList();
                  if ($parent_ca->isPublishable()){
			foreach ($dep_ca->requested_certifications as $tmp_rc) {
				if (!in_array($tmp_rc->cert_id, $existing_certs) && $tmp_rc->test_results->result == TestResults::PASS) {
					$pc = new ProductCertifications();
					$pc->cid = $this->productId2Cid($this->product_id);
					$pc->cert_id = $tmp_rc->cert_id;
					if (!$pc->save()){
						// get the errors 
						$pc_errors = print_r($pc->errors, true);
						$this->addError('product_id', "Unable to save published certification: pc_errors = $pc_errors");
						return false;
					}
				}
			}
		}
		return true;
	}
}