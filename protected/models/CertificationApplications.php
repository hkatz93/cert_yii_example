<?php

/**
 * This is the model class for table "certification_applications".
 *
 * The followings are the available columns in table 'certification_applications':
 * @property string $app_id
 * @property string $product_id
 * @property integer $certification_type
 * @property integer $recert_type_id
 * @property integer $split_parent_app_id
 * @property integer $import_parent_app_id
 * @property string $requested_by
 * @property string $company_contact
 * @property string $date_submitted
 * @property string $certifying_lab
 * @property string $date_lab_accepted
 * @property string $date_finalized_results
 * @property string $date_staff_reviewed
 * @property string $date_returned_to_member
 * @property string $date_certified
 * @property integer $status
 * @property string $staff_notes
 * @property string $committee_notes
 * @property string $lab_notes
 * @property integer $pending_os_id 
 * @property integer $initial_os_id 
 * @property string $pending_os_other
 * @property string $initial_os_other
 * @property string $pending_firmware
 * @property string $initial_firmware
 * @property string $pending_wireless_chipset	
 * @property string $initial_wireless_chipset	
 * @property string $member_resubmit_notes	
 * @property integer $hold
 * @property string $publish_on
 * @property string $deferred_date
 * @property string $differences
 * @property string $dependent_configuration
 * @property string $module_changes
 * @property string $recert_changes_hw
 * @property string $recert_changes_fw
 * @property string $recert_changes_sw
 * @property string $recert_changes_os
 * @property string $recert_changes_other
 * @property string $auto_delete
 * @property string $test_plan
 * @property integer $test_engine
 * @property string $frequency_band_mode
 * @property integer $certified_tx_spatial_stream_24
 * @property integer $certified_rx_spatial_stream_24
 * @property integer $certified_tx_spatial_stream_50
 * @property integer $certified_rx_spatial_stream_50
 * @property integer $certified_tx_spatial_stream_50_ac
 * @property integer $certified_rx_spatial_stream_50_ac
 * @property integer $agree_single_stream
 * @property string $external_registrar_support
 * @property integer $internal_registrar_support
 * 
 *
 * The followings are the available model relations:
 * @property array $requested_certifications array of RequestedCertifications
 * @property array $certifications array of Certifications
 * @property object $labs instance of Labs
 * @property object $users instance of Users
 */
class CertificationApplications extends AuditedActiveRecord
{
        /**
        * @var integer New Application
        */
        const TYPE_NEW = 9;
        /**
        * @var integer additional certifications
        */
        const TYPE_ADDITIONAL = 10;
        /**
        * @var integer Recertification
        */
        const TYPE_RECERT = 11;
        /**
        * @var integer dependent on a product that is a module
        */
        const TYPE_DEPENDENT = 12;
        /**
        * @var integer a transfer of a certification from one company to another
        */
        const TYPE_TRANSFER = 22;

        /**
         * @var integer start of certification application
         */
        const STATUS_STEP1 = 1;

        /**
         * @var integer enter product details and select certifications
         */
        const STATUS_STEP2 = 2;

        /**
         * @var integer lab selection
         */
        const STATUS_STEP3 = 3;

        /**
         * @var integer lab selected
         */
        const STATUS_STEP4 = 4;

        /**
         * @var integer staff approval
         */
        const STATUS_STEP5 = 5;

        /**
         * @var integer lab oversight committee (obsolete)
         */
        const STATUS_STEP6 = 6;

        /**
         * @var integer application has been approved by staff
         */
        const STATUS_COMPLETE = 7;

        /**
         * @var integer place this application on hold
         */
        const STATUS_HOLD = 23;

         /**
         * @var integer place this application on hold
         */
        const STATUS_FAILED = 19;

        /**
         * @var integer although a user may request > 3 spatial streams, the max testable is 3
         */
        const MAX_TESTABLE_STREAMS_IN_LAB_RESULTS = 3;

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
         * @var integer recert type is Hardware or OS changes. These require a re-testing and a charge to customer
         */
        const RECERT_TYPE_ID_HARDWARE = 1;

     /**
         * @var integer recert type is Firmware/Software (not OS). These require a re-testing, but no charge to customers if done by ATL.
         */
        const RECERT_TYPE_ID_FIRMWARE_AFFECTING = 2;

      /**
         * @var integer recert type is cosmetic only.. These do not require any testing, just approval from staff. (no charge either).
         */
	const RECERT_TYPE_ID_FIRMWARE_COSMETIC = 3;


        protected $activity_log_table_name = 'applications';
        /**
	 * Returns the static model of the specified AR class.
	 * @return CertificationApplications the static model class
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
		return 'certification_applications';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('staff_notes, committee_notes, lab_notes, differences, dependent_configuration, module_changes, pending_os_id, 
					initial_os_id, pending_os_other, initial_os_other, pending_firmware, initial_firmware, pending_wireless_chipset, 
					initial_wireless_chipset, recert_changes_hw, recert_changes_fw, recert_changes_sw, recert_changes_os, recert_changes_other', 'safe'),
			array('certification_type, status, hold, test_engine, certified_tx_spatial_stream_24, certified_rx_spatial_stream_24, certified_tx_spatial_stream_50, certified_rx_spatial_stream_50, agree_single_stream', 'numerical', 'integerOnly'=>true),
                        array('certified_tx_spatial_stream_24, certified_rx_spatial_stream_24, certified_tx_spatial_stream_50, certified_rx_spatial_stream_50', 'in', 'range'=>range(0,self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS)),
                        array('certified_tx_spatial_stream_50_ac, certified_rx_spatial_stream_50_ac',  'in', 'range'=>range(0,self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS)),
                        array('frequency_band_mode', 'in', 'range'=>array('', 'selectable','concurrent')),
			array('product_id, requested_by, certifying_lab, frequency_band_mode', 'length', 'max'=>10),
			array('company_contact', 'length', 'max'=>128),
			//array('test_engine', 'validateTestEngine', 'on'=>'update'),
			array('test_plan', 'length', 'max'=>28),
			array('date_submitted, date_lab_accepted, date_finalized_results, date_staff_reviewed, date_certified, deferred_date, auto_delete', 'safe'),
                        array('product_id', 'required'),
                        array('certification_type', 'validateType'),
                        array('split_parent_app_id', 'validateSplitParentAppId'),
                        array('import_parent_app_id', 'validateImportParentAppId'),
                        array('pending_os_other', 'validatePendingOsOther'),
                        array('initial_os_other', 'validateInitialOsOther'),
                      //  array('pending_os_id', 'validatePendingOsId'),
                       // array('initial_os_id', 'validateInitialOsId'),
                        array('publish_on', 'in', 'range'=>$this->publishOnList(), 'message'=>'publish on must be one of the following: '.(implode(',', $this->publishOnList()))),
                       array('recert_type_id', 'validateRecertTypeId'),
					
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('app_id, product_id, certification_type, recert_type_id, requested_by, company_contact, split_parent_app_id, import_parent_app_id, 
				date_submitted, certifying_lab, date_lab_accepted, date_finalized_results, date_staff_reviewed, 
				date_certified, status, staff_notes, committee_notes, lab_notes, hold, publish_on, deferred_date, 
				differences, dependent_configuration, module_changes, auto_delete, test_plan, test_engine, 
				frequency_band_mode, certified_tx_spatial_stream_24, certified_rx_spatial_stream_24, 
				certified_tx_spatial_stream_50, certified_rx_spatial_stream_50, agree_single_stream', 'safe', 'on'=>'search'),
		);
	}

        /**
         * Assure that type is valid
         */
        public function validateType(){
            $ok_list = array_keys($this->typeOptionList());
            if (!in_array($this->certification_type, $ok_list)){
                $this->addError('type', 'type does not match any known certification type');
            }
        }
	 //public function validatePendingOsId(){
//			 $ok_list = array_keys($this->osList());
//			 if (!in_array($this->pending_os_id, $ok_list)){
  //              $this->addError('pending_os_id', 'pending_os_id does not match any known os type');
    //        }
	 //}
//	public function validateInitialOsId(){
//			 $ok_list = array_keys($this->osList());
//			 if (!in_array($this->initial_os_id, $ok_list)){
  //              $this->addError('initial_os_id', 'initial_os_id does not match any known os type');
    //        }
	 //}	
	public function validateRecertTypeId(){
	    if ($this->certification_type == self::TYPE_RECERT) {
		$ok_list = array_keys($this->recertTypeIdOptionList());
		if (!in_array($this->recert_type_id, $ok_list)){
			$this->addError('recert_type_id', 'Re-certification type id does not match any known type');
		}
	   }
	}
	
	public function validateSplitParentAppId(){
		if (empty($this->split_parent_app_id)) {
			return; // end validation since blanks are ok
		}
		$ca = self::model()->findByPk($this->split_parent_app_id);
		if (!($ca instanceof CertificationApplications)) {
			$this->addError('split_parent_app_id', 'import parent app id does not match any known module product app');
		}
	}
	
	/**
	 * assure that the import_parent_app_id exists
	 */
	public function validateImportParentAppId(){
		if (empty($this->import_parent_app_id)) {
			return; // end validation since blanks are ok
		}
		$ca = self::model()->findByPk($this->import_parent_app_id);
		if (!($ca instanceof CertificationApplications)) {
			$this->addError('import_parent_app_id', 'import parent app id does not match any known module product app');
		}
		elseif ($ca->product_id == $this->product_id) {
			$this->addError('import_parent_app_id', 'import parent app id cannot link to the same product as the application');
		}
		else {
			$prod = Products::model()->findByPk($ca->product_id);
			if (empty($prod->is_module)) {
				$this->addError('import_parent_app_id', 'parent product is not a module');
			}
		}
        }

		public function validatePendingOsOther() {
				$os_other = trim($this->pending_os_other);
				if ($this->pending_os->name == 'Other' && empty($os_other)) {
						 $this->addError('pending_os_other', 'Other operating system cannot be blank when"Other" selected');
				}
		}
		public function validateInitialOsOther() {
				$os_other = trim($this->initial_os_other);
				if ($this->initial_os->name == 'Other' && empty($os_other)) {
						 $this->addError('initial_os_other', 'Other operating system cannot be blank when"Other" selected');
				}
		}
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'requested_certifications' => array(self::HAS_MANY, 'RequestedCertifications', 'app_id'),
                    'certifications' => array(self::MANY_MANY, 'Certifications', 'requested_certifications(app_id, cert_id)'),
                    'labs' => array(self::BELONGS_TO, 'Labs', 'certifying_lab'),
                    'users' => array(self::BELONGS_TO, 'Users', 'requested_by'),
                    'products' => array(self::BELONGS_TO, 'Products', 'product_id'),
                    'pending_os' => array(self::BELONGS_TO, 'Os', 'pending_os_id'),
                    'initial_os' => array(self::BELONGS_TO, 'Os', 'initial_os_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'app_id' => 'App ID',
			'product_id' => 'Product ID',
			'certification_type' => 'Certification Type',
			'recert_type_id' => 'Re-certification Type',
			'requested_by' => 'Requested By',
			'company_contact' => 'Company Contact',
			'date_submitted' => 'Date Submitted',
			'certifying_lab' => 'Certifying Lab',
			'date_lab_accepted' => 'Date Lab Accepted',
			'date_finalized_results' => 'Date Finalized Results',
			'date_staff_reviewed' => 'Date Staff Reviewed',
			'date_certified' => 'Date Certified',
			'status' => 'Status',
			'staff_notes' => 'Staff Notes',
			'committee_notes' => 'Committee Notes',
			'lab_notes' => 'Lab Notes',
			'pending_firmware' => 'Pending Firmware Change',
			'recert_changes_hw'=>'Recert Hardware Changes', 
			'recert_changes_fw'=>'Recert Firmware Changes', 
			'recert_changes_sw'=>'Recert Software Changes', 
			'recert_changes_os'=>'Recert OS Changes', 
			'recert_changes_other'=>'Recert Other Changes', 
			'hold' => 'Hold',
			'publish_on' => 'Publish On',
			'deferred_date' => 'Deferred Date',
			'differences' => 'Differences',
			'dependent_configuration' => 'Dependent Configuration',
			'module_changes' => 'Module Changes',
			'auto_delete' => 'Auto Delete',
			'test_plan' => 'Test Plan',
			'test_engine' => 'Test Engine',
			'frequency_band_mode' => 'Frequency Band Mode',
			'certified_tx_spatial_stream_24' => 'Certified Tx Spatial Stream 2.4 GHz',
			'certified_rx_spatial_stream_24' => 'Certified Rx Spatial Stream 2.4 GHz',
			'certified_tx_spatial_stream_50' => 'Certified Tx Spatial Stream 5.0 GHz ac',
			'certified_rx_spatial_stream_50' => 'Certified Rx Spatial Stream 5.0 GHz ac',
			'agree_single_stream' => 'Agree Single Stream',
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

		$criteria->compare('app_id',$this->app_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('certification_type',$this->certification_type);
		$criteria->compare('recert_type_id',$this->recert_type_id);
		$criteria->compare('requested_by',$this->requested_by);
		$criteria->compare('company_contact',$this->company_contact,true);
		$criteria->compare('date_submitted',$this->date_submitted,true);
		$criteria->compare('certifying_lab',$this->certifying_lab,true);
		$criteria->compare('date_lab_accepted',$this->date_lab_accepted,true);
		$criteria->compare('date_finalized_results',$this->date_finalized_results,true);
		$criteria->compare('date_staff_reviewed',$this->date_staff_reviewed,true);
		$criteria->compare('date_certified',$this->date_certified,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('staff_notes',$this->staff_notes,true);
		$criteria->compare('committee_notes',$this->committee_notes,true);
		$criteria->compare('lab_notes',$this->lab_notes,true);
		$criteria->compare('hold',$this->hold);
		$criteria->compare('publish_on',$this->publish_on,true);
		$criteria->compare('deferred_date',$this->deferred_date,true);
		$criteria->compare('differences',$this->differences,true);
		$criteria->compare('dependent_configuration',$this->dependent_configuration,true);
		$criteria->compare('module_changes',$this->module_changes,true);
		$criteria->compare('auto_delete',$this->auto_delete,true);
		$criteria->compare('test_plan',$this->test_plan,true);
		$criteria->compare('test_engine',$this->test_engine);
		$criteria->compare('frequency_band_mode',$this->frequency_band_mode,true);
		$criteria->compare('certified_tx_spatial_stream_24',$this->certified_tx_spatial_stream_24);
		$criteria->compare('certified_rx_spatial_stream_24',$this->certified_rx_spatial_stream_24);
		$criteria->compare('certified_tx_spatial_stream_50',$this->certified_tx_spatial_stream_50);
		$criteria->compare('certified_rx_spatial_stream_50',$this->certified_rx_spatial_stream_50);
		$criteria->compare('certified_tx_spatial_stream_50_ac',$this->certified_tx_spatial_stream_50_ac);
		$criteria->compare('certified_rx_spatial_stream_50_ac',$this->certified_rx_spatial_stream_50_ac);
		$criteria->compare('agree_single_stream',$this->agree_single_stream);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

        /**
         * defines Yii named scopes
         * NOTE: "publishable" scope only indicates it may have already been published
         * @return array 
         * 
         * @see CertificationApplications::isPublishable()
         */
        public function scopes() {
            return array(
                'completed'=>array(
                    'condition'=>'status='. self::STATUS_COMPLETE,
                ),
                'publishable'=>array(
                    'condition'=>'status='. self::STATUS_COMPLETE 
                        ." AND deferred_date <= NOW()"
                        ." AND publish_on !='Never'"
                        ." AND hold = 0",
                    'order'=>'date_certified DESC'
                ),
                'most_recent'=>array(
                    'order'=>'app_id DESC',
                    'limit'=>10,
                ),
                'oldest'=>array(
                    'order'=>'app_id ASC',
                    'limit'=>10,
                ),
            );
        }

        /**
         * whether an app meets the criteria to be (or to have been) published
         * @return boolean true if application is fit to be published (or already had been)
         * 
         * @see CertificationApplications::scope()
         */
        public function isPublishable() {
            $ok = true;
            $ok = (!empty($this->hold)) ? false : $ok; // zero or null are both ok
            $ok = ($this->publish_on == 'Never') ? false : $ok;
            $ok = ($this->status != self::STATUS_COMPLETE) ? false : $ok;
            $ok = (strtotime($this->deferred_date) > time()) ? false : $ok;
            return $ok;    
        }
	/**
	 * 
	 * @return boolean true if application has been imported from another application
	 */	
	public function isImported() {
	   $ok = (!empty($this->import_parent_app_id)) ? true : false;
	   return $ok;
	}
        
        /**
         * Deletes all related information based directly on application
         * NOTE: this also includes certifications that are already published, this might be too agressive.
         * @return boolean
         * @todo update activity_log table with the transaction details
         * @todo delete from product_certifications when no other app could have published it (from re-certs)
         */
        public function beforeDelete() {

            parent::beforeDelete();

            // check to make sure this this application is not the last one of the product
            $related_apps = CertificationApplications::model()->findAll('product_id=:product_id', array('product_id'=>$this->product_id));
            if (count($related_apps) <= 1) {
                $this->addError('app_id', 'cannot delete the last application from a product');
                return false;
            }

            // prevent cert_n deletions since they need special handling
            if (0 && in_array($this->cert_id,
                    array(
                        Certifications::CERT_N,
                        Certifications::CERT_N_APPROVED,
                        Certifications::CERT_N_APPROVED_TEST_ENGINE))) {
                return false;
            }
            //$row = $model->getDbConnection()->createCommand('SHOW TABLE STATUS LIKE TableName')->queryRow();

            $connection=Yii::app()->db;   // assuming you have configured a "db" connection
            
            $app_id = $this->app_id;
            
            $sql = ""
                    ." FROM certification_applications as ca"
                    ." INNER JOIN requested_certifications as rc ON (ca.app_id = rc.app_id)"
                    ." LEFT JOIN test_results as tr ON (tr.request_id = rc.request_id)"
                    ." LEFT JOIN test_data as td ON (td.request_id = tr.request_id)"
                    ." INNER JOIN products as p ON (p.product_id = ca.product_id)"
                    ." LEFT JOIN product_certifications as pc ON (p.cid = pc.cid and pc.cert_id = rc.cert_id)"
                    ." WHERE ca.app_id=$app_id";
                    //." WHERE ca.product_id=:product_id";

            $sql_count = "select count(*) as count".$sql;
            $sql_delete = "DELETE rc, tr, td ".$sql;
            //$sql_delete = "DELETE rc, tr, td, pc ".$sql; // do not remove from product_certifications
            
            // first check how many rows will be deleted

            $command=$connection->createCommand($sql_delete);
            //$command->bindParam(":app_id",$this->app_id,PDO::PARAM_INT);
            
            $rv = $command->execute();

            if ($rv){
                Yii::log("[deleted $rv rows]");
                return true;
            }
            else {
                Yii::log("problem with beforeDelete for CertificationApplications model");
                return false; // this will halt the deletion
            }
        }

        /**
         * Adds a certification to an application with either a pass/fail/not tested test_result
         * 
         * @param integer $cert_id
         * @param integer $test_result TestResults::(PASS|FAIL|NOT_TESTED)
         * @return boolean
         */
        public function addCertification($cert_id, $test_result=TestResults::PASS){

            // check to see if this application already has this requested certification
            $old_rc = RequestedCertifications::model()->find('app_id=:app_id and cert_id=:cert_id',
                    array('app_id'=>$this->app_id, 'cert_id'=>$cert_id));
            if ($old_rc) {
                    $this->addError('cert_id', 'certification already exists for this application');
                    return false;
            }

            $tmp = array();
            $tmp['app_id'] = $this->app_id;
            $tmp['cert_id'] = $cert_id;
            $rc = new RequestedCertifications();
            $rc->setAttributes($tmp);
            //print_r($tmp);
            $rv = $rc->save();
            if (!$rv){
                $this->addError("cert_id","unable to update requested certifications for this application (app_id = {$tmp['app_id']})".print_r($rc->getErrors(),true));
                return false;
            }
            // now create the row in test_results
            $tr = new TestResults();
            $tmp = array();
            $tmp['request_id']= $rc->request_id;
            if ($test_result == TestResults::PASS){
                $tmp['result']= TestResults::PASS;
            }
            elseif ($test_result == TestResults::FAIL) {
                $tmp['result']= TestResults::FAIL;
            }
            else {
                $tmp['result']= TestResults::NOT_TESTED;
            }
            $tr->setAttributes($tmp);
            //print_r($tmp);
            $rv = $tr->save();
            if (!$rv){
                $this->addError("unable to save test results for this certification");
                return false;
            }
            else {
                return true;
            }
            
        }

        /**
         * Remove certication from the application (does not affect product)
         *
         * @param integer $cert_id
         * @return boolean true when success
         */
        public function deleteCertification($cert_id) {
            $rc = RequestedCertifications::model()->find('cert_id=:cert_id AND app_id=:app_id', array('cert_id'=>$cert_id, 'app_id'=>$this->app_id));
            $rv = $rc->delete();
            return $rv;
        }

        /**
         * Sets multiple certifications on application at one time
         * CAVEAT: currently defaults the results to PASS
         *
         * @todo allow the results to individual statuses rather all being the same
         * @param array $cert_id_list
         * @return boolean
         */
        public function setCertifications($cert_id_list){

            if (!is_array($cert_id_list)) {
                throw new InvalidArgumentException("cert_id_list must be an array");
            }
            // base the certification test on what the status of the application is
            // if the product has gone through lab testing, set it to PASS, otherwise NOT TESTED
            if ($this->status < self::STATUS_STEP4){
                $test_result = TestResults::NOT_TESTED;
            }
            else {
                $test_result = TestResults::PASS;
            }

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

            // prevent errors from non-arrays
            $existing_cert_id_list = (is_array($existing_cert_id_list)) ? $existing_cert_id_list : array();
            $cert_id_list = (is_array($cert_id_list)) ? $cert_id_list : array();

            $delete_list = array_diff($existing_cert_id_list, $cert_id_list);
            $add_list = array_diff($cert_id_list, $existing_cert_id_list);

            $has_error = false;

            // important to add the new ones first
            // for cert_n we cannot delete if cert_b does not exist
            foreach ($add_list as $cert_id){
                $rv = $this->addCertification($cert_id, $test_result); 
                if (!$rv){
                    //print "unable to add cert_id=$cert_id, ";
                }
                $has_error = ($rv == false) ? true : $has_error;
            }

            foreach ($delete_list as $cert_id){
                $rv = $this->deleteCertification($cert_id);
                if (!$rv){
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

        
        public function beforeSave(){
            parent::beforeSave();
		   $rv = $this->_beforeSaveOs(); // odd behaviour causing test failures when called after _updateLabResults()
            $rv = $this->_beforeSaveConvertDates();
            $rv = $this->_updateLabResults();
		   

	  // prevent assigning re-cert type id if application is not a re-cert
	   if ($this->certification_type != self::TYPE_RECERT && !empty($this->recert_type_id)) {
		   return false;
	   }
			
            // remove the published applications if the application is pushed back
            // from completed status to anything else and app is not re-cert 
            //
            // be careful not to use !== here since properties get turned into strings
            if ($this->certification_type != self::TYPE_RECERT
            && $this->status != self::STATUS_COMPLETE) {
                // look up pre-existing value
                $ca = self::model()->findByPk($this->app_id);
                //print "ca->app_id = {$ca->app_id}";
                if ($ca->status == self::STATUS_COMPLETE){
                    // remove from ProductCertifcations
                    // very specifically do not change anyother table

                    // get list of certifications for this application
                    $prod = Products::model()->findByPk($this->product_id);
                    $pc_list = $prod->getProductCertifications();
		    if (empty($pc_list)){
			$pc_list = array();
		    }
		    elseif (!is_array($pc_list)){
			$pc_list = array($pc_list);
		    }
                    $rc_list = $ca->requested_certifications;
                    $rc_cert_id_list = array();
                    foreach ($rc_list as $rc){
                        $rc_cert_id_list[] = $rc->cert_id;
                    }
                    foreach ($pc_list as $pc){
                        if(in_array($pc->cert_id, $rc_cert_id_list)) {
                            $pc->nonCascadingDelete();
                        }
                    }
                    
                }
            }

            if (!$rv){
                return false;
            }
            
            return true;
        }

        public function afterFind(){
            parent::afterFind();
            $this->_afterFindConvertDates();

        }

        /**
         * @param string in some date format
         * @return string in YYYY-MM-DD HH:mm:SS format
         *
         *  @todo consider conditions when a NULL or blank is more appropriate than 0000-00-00, in cert sometimes you see 1969-01-01
         */
        public function datestr2YYYYMMDD($datestr, $show_time=true){
            //print "datestr = ".$datestr;
            if ($datestr == '0000-00-00' || $datestr =='0'){
                return '0000-00-00';
            }
            if ($datestr == '0000-00-00 00:00:00'){
                return '0000-00-00 00:00:00';
            }
            if ($show_time){
                $rv = date("Y-m-d H:i:s", strtotime($datestr));
            }
            else {
                $rv = date("Y-m-d", strtotime($datestr));
            }
            //print ", rv = $rv";
           return $rv;
       }
        
        /**
         *
         *
         * @param integer $time (NOTE: special case when 0)
         * @param boolean $show_time
         * @return string MM/DD/YYYY ##:##:## 
         */
        public function time2MMDDYYYY($time, $show_time=true){
            if ($show_time){
                return ($time == 0) ? '0000-00-00 00:00:00' : date('n/j/Y H:i:s', $time);
            }
            else {
                return ($time == 0) ? '0000-00-00' : date('n/j/Y', $time);
            }
        }
        
        /**
         * @todo allow product updates without infinite loops when app changed by product
         * @return boolean
         */
        public function afterSave() {
            $rv = parent::afterSave();


            return $rv;
            
            // this can cause problems when product edits trigger this nmethod-> infinite loop
            // for now I will not update the product from the application
            //$rv = $this->_updateProduct();
            if (!$rv){
                return false;
            }
            return true;
        }

        /**
         * if changes to application are successful, make sure to move them to the product
         * currently this just affects certified spatial streams
         * 
         * @return boolean true
         */
        /*
        public function _updateProduct(){
            // if this certification is publishable, then the product can be updated accordingly
            // check to see if this app is publishable
            $ca = CertificationApplications::model()->publishable()->find('app_id=:app_id', array('app_id'=>$this->app_id));
            if ($ca instanceof CertificationApplications){
                if (0){
                    print "updating product\n";

                    print "\$ca->certified_tx_spatial_stream_24 =".$ca->certified_tx_spatial_stream_24 ."\n";
                    print "\$ca->certified_rx_spatial_stream_24 =".$ca->certified_rx_spatial_stream_24 ."\n";
                    print "\$ca->certified_tx_spatial_stream_50 =".$ca->certified_tx_spatial_stream_50 ."\n";
                    print "\$ca->certified_rx_spatial_stream_50 =".$ca->certified_rx_spatial_stream_50 ."\n";
                }
                // grab the product
                $prod = Products::model()->findByPk($ca->product_id);
                
                if (0){
                    print "\$prod->certified_tx_spatial_stream_24 =".$prod->certified_tx_spatial_stream_24 ."\n";
                    print "\$prod->certified_rx_spatial_stream_24 =".$prod->certified_rx_spatial_stream_24 ."\n";
                    print "\$prod->certified_tx_spatial_stream_50 =".$prod->certified_tx_spatial_stream_50 ."\n";
                    print "\$prod->certified_rx_spatial_stream_50 =".$prod->certified_rx_spatial_stream_50 ."\n";
                }
                
                if ($ca->certified_tx_spatial_stream_24 > $prod->certified_tx_spatial_stream_24) {
                    //print ("updating certified_tx_spatial_stream_24");
                    $prod->certified_tx_spatial_stream_24 = $ca->certified_tx_spatial_stream_24;
                }
                if ($ca->certified_rx_spatial_stream_24 > $prod->certified_rx_spatial_stream_24) {
                    //print ("updating certified_rx_spatial_stream_24");
                    $prod->certified_rx_spatial_stream_24 = $ca->certified_rx_spatial_stream_24;
                }
                if ($ca->certified_tx_spatial_stream_50 > $prod->certified_tx_spatial_stream_50) {
                    //print ("updating certified_tx_spatial_stream_50");
                    $prod->certified_tx_spatial_stream_50 = $ca->certified_tx_spatial_stream_50;
                }
                if ($ca->certified_rx_spatial_stream_50 > $prod->certified_rx_spatial_stream_50) {
                    //print ("updating certified_rx_spatial_stream_50");
                    $prod->certified_rx_spatial_stream_50 = $ca->certified_rx_spatial_stream_50;
                }
                $rv = $prod->save();
                if ($rv){
                        //print "successful save for product\n";
                //    return true;
                }
                else {
                    //print "FAILED save for product\n";
                //    $this->addError('product_id', 'unable to update product');
                }
            }
            else {
                //print "-- this application is not publishable\n";
            }
            return true;
        }
         *
         */

        /**
         * change dates back to database friendly format
         */
        private function _beforeSaveConvertDates() {
            $this->date_submitted = $this->datestr2YYYYMMDD($this->date_submitted);

            //print __FUNCTION__.": date before =". $this->date_lab_accepted; print "\n";

            $this->date_lab_accepted = $this->datestr2YYYYMMDD($this->date_lab_accepted);
            //print __FUNCTION__.": date after = ". $this->date_lab_accepted; print "\n";
            
            $this->date_finalized_results = $this->datestr2YYYYMMDD($this->date_finalized_results);
            $this->date_staff_reviewed = $this->datestr2YYYYMMDD($this->date_staff_reviewed);
            $this->date_certified = $this->datestr2YYYYMMDD($this->date_certified);
            $this->deferred_date = $this->datestr2YYYYMMDD($this->deferred_date, false);

            //$this->deferred_date = $this->time2MMDDYYYY(strtotime($this->deferred_date));
        }

	private function _beforeSaveOs() {
			
			if ($this->pending_os_id != Os::OS_ID_OTHER) {
					$this->pending_os_other = '';
			}
			if ($this->initial_os_id != Os::OS_ID_OTHER) {
					$this->initial_os_other = '';
			}
	}
        /**
         * change dates to MM-DD-YYYY format
         */
        private function _afterFindConvertDates(){
            $this->date_submitted = $this->time2MMDDYYYY(strtotime($this->date_submitted));
            $this->date_lab_accepted = $this->time2MMDDYYYY(strtotime($this->date_lab_accepted));
            $this->date_finalized_results = $this->time2MMDDYYYY(strtotime($this->date_finalized_results));
            $this->date_staff_reviewed = $this->time2MMDDYYYY(strtotime($this->date_staff_reviewed));
            $this->date_certified = $this->time2MMDDYYYY(strtotime($this->date_certified));
            $this->deferred_date = $this->time2MMDDYYYY(strtotime($this->deferred_date), false);
        }

        /**
         * updates test_data according to changes made in application
         * @return boolean
         * @uses TestData, TestData::maxInt()
         * @todo consider implications of application status if this becomes more than a super-admin tool
         */
        private function _updateLabResults(){

            // see if we can grab these test_data
            if (1 || $this->status >= self::STATUS_STEP4) {


            $td1_ap = TestData::model()->getTestDataByAppIdAndFieldId($this->app_id, TestFields::AP_MAX_TX_STREAMS_24_CERT_N_APPROVED);
            $td2_ap = TestData::model()->getTestDataByAppIdAndFieldId($this->app_id, TestFields::AP_MAX_RX_STREAMS_24_CERT_N_APPROVED);
            $td3_ap = TestData::model()->getTestDataByAppIdAndFieldId($this->app_id, TestFields::AP_MAX_TX_STREAMS_50_CERT_N_APPROVED);
            $td4_ap = TestData::model()->getTestDataByAppIdAndFieldId($this->app_id, TestFields::AP_MAX_RX_STREAMS_50_CERT_N_APPROVED);

            $td1_sta = TestData::model()->getTestDataByAppIdAndFieldId($this->app_id, TestFields::STA_MAX_TX_STREAMS_24_CERT_N_APPROVED);
            $td2_sta = TestData::model()->getTestDataByAppIdAndFieldId($this->app_id, TestFields::STA_MAX_RX_STREAMS_24_CERT_N_APPROVED);
            $td3_sta = TestData::model()->getTestDataByAppIdAndFieldId($this->app_id, TestFields::STA_MAX_TX_STREAMS_50_CERT_N_APPROVED);
            $td4_sta = TestData::model()->getTestDataByAppIdAndFieldId($this->app_id, TestFields::STA_MAX_RX_STREAMS_50_CERT_N_APPROVED);
            


            if ($td1_ap instanceof TestData){
                //print 'updating lab results for '.$td1_ap->test_fields->field_name." ==> {$this->certified_tx_spatial_stream_24}\n";
                $td1_ap->data = TestData::maxInt($this->certified_tx_spatial_stream_24, self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
                $td1_ap->save();
            }
            if ($td2_ap instanceof TestData){
                //print 'updating lab results for '.$td2_ap->test_fields->field_name." ==> {$this->certified_rx_spatial_stream_24}\n";
                $td2_ap->data = TestData::maxInt($this->certified_rx_spatial_stream_24, self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
                $td2_ap->save();
             }
            if ($td3_ap instanceof TestData){
                //print 'updating lab results for '.$td3_ap->test_fields->field_name." ==> {$this->certified_tx_spatial_stream_50}\n";
                $td3_ap->data = TestData::maxInt($this->certified_tx_spatial_stream_50, self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
                $td3_ap->save();
            }
            if ($td4_ap instanceof TestData){
                //print 'updating lab results for '.$td4_ap->test_fields->field_name." ==> {$this->certified_rx_spatial_stream_50}\n";
                $td4_ap->data = TestData::maxInt($this->certified_rx_spatial_stream_50, self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
                $td4_ap->save();
            }

            if ($td1_sta instanceof TestData){
                //print 'updating lab results for '.$td1_sta->test_fields->field_name." ==> {$this->certified_tx_spatial_stream_24}\n";
                $td1_sta->data = TestData::maxInt($this->certified_tx_spatial_stream_24, self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
                $td1_sta->save();
            }
            if ($td2_sta instanceof TestData){
                //print 'updating lab results for '.$td2_sta->test_fields->field_name." ==> {$this->certified_rx_spatial_stream_24}\n";
                $td2_sta->data = TestData::maxInt($this->certified_rx_spatial_stream_24, self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
                $td2_sta->save();
            }
            if ($td3_sta instanceof TestData){
                //print 'updating lab results for '.$td3_sta->test_fields->field_name." ==> {$this->certified_tx_spatial_stream_50}\n";;

                $td3_sta->data = TestData::maxInt($this->certified_tx_spatial_stream_50, self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
                $td3_sta->save();
            }
            if ($td4_sta instanceof TestData){
                //print 'updating lab results for '.$td3_sta->test_fields->field_name." ==> {$this->certified_rx_spatial_stream_50}\n";
                $td3_sta->data = TestData::maxInt($this->certified_rx_spatial_stream_50, self::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
                $td4_sta->save();
            }
                //$td1_ap->data = $this->certified_

            }
        
            return true;
        }

        /**
         * options possible for publish_on attribute
         * @return array key-value
        */
        public function publishOnList() {
            return array(
                self::PUBLISH_ON_CERTIFICATION_DATE => self::PUBLISH_ON_CERTIFICATION_DATE,
                self::PUBLISH_ON_DEFERRED_DATE => self::PUBLISH_ON_DEFERRED_DATE,
                self::PUBLISH_ON_NEVER => self::PUBLISH_ON_NEVER);
        }

        /**
         *
         * @return array key-value for certification type where key matches type property
         */
        public function typeOptionList(){
            return array(
                self::TYPE_NEW => 'New',
                self::TYPE_ADDITIONAL => 'Additional',
                self::TYPE_RECERT => 'Re-certification',
                self::TYPE_DEPENDENT => 'Dependent',
                self::TYPE_TRANSFER => 'Transfer',
            );
        }
		
        /**
         *
         * @return array key-value for certification type where key matches type property
         */
        public function recertTypeIdOptionList(){
            return array(
                self::RECERT_TYPE_ID_HARDWARE => 'Hardware/OS',
                self::RECERT_TYPE_ID_FIRMWARE_AFFECTING => 'Firmware/Software',
                self::RECERT_TYPE_ID_FIRMWARE_COSMETIC => 'Cosmetic',
            );
        }

        /**
         * returns a distinct list of company contacts
         * 
         * @param string $name
         * @param integer $limit
         * @return array
         */
        public function getCompanyContactsLikeNameList($name, $limit=50){
            if (!preg_match("/\d+/", $limit)){
                $limit = 50;
            }
            $sql = "SELECT DISTINCT company_contact"
                . " FROM certification_applications"
                . " WHERE company_contact LIKE :company_contact"
                . " ORDER BY company_contact"
                . " LIMIT $limit ";
               // . " LIMIT :limit";

            $connection = Yii::app()->db;   // assuming you have configured a "db" connection
            $command = $connection->createCommand($sql);
            $name = "%".$name."%";
            $command->bindParam(":company_contact",$name,PDO::PARAM_STR);
            //$command->bindParam(":limit",$limit,PDO::PARAM_INT);
            $rows = $command->queryAll();
            return $rows;
        }

        public function statusList(){
            return array(
                self::STATUS_STEP1 => '1. Application',
                self::STATUS_STEP2 => '2. Lab Selection',
                self::STATUS_STEP3 => '3. Lab Acceptance',
                self::STATUS_STEP4 => '4. Testing',
                self::STATUS_STEP5 => '5. Staff Approval',
                //self::STATUS_STEP6 => '6. Oversight Committee', // obsolete
                self::STATUS_COMPLETE => '7. Complete',
                self::STATUS_FAILED => 'Failed',
                self::STATUS_HOLD => 'On Hold',
            );
        }

         /**
         * we want all of the certifications sorted in a special way
         * seems easiest to use SQL rather than a complex active record
         *
         * @return array from queryAll()
         */
        public function certificationsArray() {
            $sql = "SELECT DISTINCT cc.category, c.cert_id, c.display_name, rc.request_id, tr.result"
                    
                    . " FROM certification_applications as ca "
                    . " INNER JOIN requested_certifications as rc ON (rc.app_id = ca.app_id)"
                    . " INNER JOIN certifications as c ON (c.cert_id = rc.cert_id)"
                    . " INNER JOIN certification_categories as cc ON (cc.category_id = c.category_id)"
                    . " LEFT JOIN test_results as tr ON (tr.request_id = rc.request_id)"
                    . " WHERE ca.app_id=:app_id"
                    . " ORDER BY cc.placement, c.placement";


            $connection=Yii::app()->db;   // assuming you have configured a "db" connection
            $command=$connection->createCommand($sql);
	  $app_id = $this->app_id;
            $command->bindParam(":app_id",$app_id,PDO::PARAM_INT);

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
	 * helper function, retrieve list of product_id that current application could be exported to
	 * @return array of product_id
	 */
	public function getImportReadyDependentProductIds() {
		$sql = "SELECT DISTINCT p.product_id 
			FROM products as p
			INNER JOIN certification_applications as ca ON (p.product_id = ca.product_id)
			WHERE p.parent_id=:product_id
			AND p.product_id 
			NOT IN (
				select ca.product_id from certification_applications as ca
				where ca.import_parent_app_id=:app_id
			)
		";
		$connection=Yii::app()->db;   // assuming you have configured a "db" connection
		$command=$connection->createCommand($sql);
		$app_id = $this->app_id;
		$product_id = $this->product_id;
		$command->bindParam(":app_id",$app_id,PDO::PARAM_INT);
		$command->bindParam(":product_id",$product_id,PDO::PARAM_INT);
		$rows = $command->queryAll();
		$out_ary = array();
		foreach ($rows as $row) {
			$out_ary[] = $row['product_id'];
		}
		return $out_ary;
	}
	
	/**
	 * helper function to determine which dependent product have already imported this application
	 * @return array of product_id
	 */
	public function getImportedAppProductIds() {
		$sql = "SELECT DISTINCT p.product_id 
			FROM products as p
			INNER JOIN certification_applications as ca ON (p.product_id = ca.product_id)
			WHERE p.parent_id=:product_id
			AND p.product_id 
			 IN (
				select ca.product_id from certification_applications as ca
				where ca.import_parent_app_id=:app_id
			)
		";
		$connection=Yii::app()->db;   // assuming you have configured a "db" connection
		$command=$connection->createCommand($sql);
		$app_id = $this->app_id;
		$product_id = $this->product_id;
		$command->bindParam(":app_id",$app_id,PDO::PARAM_INT);
		$command->bindParam(":product_id",$product_id,PDO::PARAM_INT);
		$rows = $command->queryAll();
		$out_ary = array();
		foreach ($rows as $row) {
			$out_ary[] = $row['product_id'];
		}
		return $out_ary;
	}
}
