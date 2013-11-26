<?php

/**
 * This is the model class for table "requested_certifications".
 *
 * The followings are the available columns in table 'requested_certifications':
 * @property string $request_id
 * @property string $app_id
 * @property string $cert_id
 * @property integer $data_fields_cert_id
 *
 * The followings are the available model relations:
 * @property object $certification_applications
 * @property object $certifications
 * @property object $test_results
 * @property array $test_data
 * 
 */
class RequestedCertifications extends CActiveRecord
{
        /**
         * @var string certification is required to have external data (such as excel upload)
         */
        const EXT_DATA_NO = 0;
        /**
         * @var string certification does not have external data (such as excel upload)
         */
        const EXT_DATA_YES = 1;
        /**
         * @var string certification might have external data (such as excel upload) but it not required
         */
        const EXT_DATA_MAYBE = 2;


	/**
	 * Returns the static model of the specified AR class.
	 * @return RequestedCertifications the static model class
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
		return 'requested_certifications';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('data_fields_cert_id', 'numerical', 'integerOnly'=>true),
                        array('cert_id', 'validateCertId'),
                        array('app_id', 'validateAppId'),
			//array('app_id, cert_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('request_id, app_id, cert_id, data_fields_cert_id', 'safe', 'on'=>'search'),
		);
	}

        public function validateCertId($attribute, $params){
            if (!in_array($this->cert_id, Certifications::model()->getCertIdList())){
                 $this->addError('cert_id','Cert Id is not known');
            }
        }

        /**
         *
         * @param type $attribute
         * @param type $params 
         * @todo disable this validation if RequestedCertificiation is newly created
         */
        public function validateAppId($attribute, $params){
            $app = CertificationApplications::model()->findByPk($this->app_id);
            if (empty($app->app_id)){
                 $this->addError('app_id','app_id does not exist');
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
                    'certification_applications' => array(self::BELONGS_TO, 'CertificationApplications', 'app_id'),
                    'certifications' => array(self::BELONGS_TO, 'Certifications', 'cert_id'),
                    'test_results' => array(self::HAS_ONE, 'TestResults', 'request_id'),
                    'test_data' => array(self::HAS_MANY, 'TestData', 'request_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'request_id' => 'Request',
			'app_id' => 'App',
			'cert_id' => 'Cert',
			'data_fields_cert_id' => 'Data Fields Cert',
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

		$criteria->compare('request_id',$this->request_id,true);
		$criteria->compare('app_id',$this->app_id,true);
		$criteria->compare('cert_id',$this->cert_id,true);
		$criteria->compare('data_fields_cert_id',$this->data_fields_cert_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

        /**
         * requires special handling for 802.11n
         * for now, prohibit deleting of certifications that have test_data
         *
         * @return boolean if false, then delete is halted
         *
         * @todo delete certifications from product_certifications if and only if no other requested certs from another app link to it
         */
        public function  beforeDelete() {
            parent::beforeDelete();

            // for now prohibit removing 802.11 Draft N
            // don't want to deal with preserving data as with other cert n's
            if ($this->cert_id == Certifications::CERT_N) {
                return false;
            }

            // prohibit deleting ABG if they have associated lab data
            // just prevent all these deletes for now (don't move data like cert_n deletes)
            //
            if (in_array($this->cert_id, array(
                    Certifications::CERT_A,
                    Certifications::CERT_A_TEST_ENGINE,
                    Certifications::CERT_B,
                    Certifications::CERT_B_TEST_ENGINE,
                    Certifications::CERT_G,
                    Certifications::CERT_G_TEST_ENGINE
                    ))) {

                if (!empty($this->test_data)){
                    $this->addError('request_id', "cannot delete certifications 802.11a, b, or g when they have associated lab data");
                    return false;
                }
            }
            
            // prevent cert_n deletions since they need special handling
            $is_cert_n = (in_array($this->cert_id,
                            array(
                                Certifications::CERT_N,
                                Certifications::CERT_N_APPROVED,
                                Certifications::CERT_N_APPROVED_TEST_ENGINE
                            ))) ? 1 : 0;
                
            

            $rv = $this->_preDeleteCleanup();
            if (!$rv) {
                //print "pre-delete failed";
                return false;
            }
            $connection=Yii::app()->db;   // assuming you have configured a "db" connection

            // delete related test data and result, except if 802.11n
            if (!$is_cert_n) {
                $sql_delete = "DELETE tr, td"
                        ." FROM certification_applications as ca"
                        ." INNER JOIN requested_certifications as rc ON (ca.app_id = rc.app_id)"
                        ." LEFT JOIN test_results as tr ON (tr.request_id = rc.request_id)"
                        ." LEFT JOIN test_data as td ON (td.request_id = tr.request_id)"
                        //." INNER JOIN products as p ON (p.product_id = ca.product_id)"
                        //." LEFT JOIN product_certifications as pc ON (p.cid = pc.cid and pc.cert_id = rc.cert_id)"
                        ." WHERE rc.request_id=:request_id";

                $command=$connection->createCommand($sql_delete);
	       $request_id = $this->request_id;
                $command->bindParam(":request_id",$request_id,PDO::PARAM_INT);

                $rv = $command->execute();

            }

            return true;
        }

        public function afterDelete() {
            parent::afterDelete();
            $this->_postDeleteMoveLabResults();
        }
        
        /**
         *
         * @return boolean
         * @todo fix unit tests to make sure that this functionality will work 
         */
        public function beforeSave() {
            return parent::beforeSave(); // this disables code below
            //parent::beforeSave();
            // check to make sure that we dont have duplicate app_id/cert_id combination
            $connection=Yii::app()->db;   
            $sql_delete = "SELECT request_id FROM requested_certifications WHERE app_id=:app_id AND cert_id=:cert_id";
                        
            $command=$connection->createCommand($sql_delete);
            $command->bindParam(":app_id",$this->app_id,PDO::PARAM_INT);
            $command->bindParam(":cert_id",$this->cert_id,PDO::PARAM_INT);
            $rows = $command->queryAll();
            if (empty($rows[0]['request_id'])){
                return true;
            }
            else {
                $this->addError('request_id', 'prevented duplicate insert for app_id and cert_id');
                return false;
            }
            
        }

        /**
         * Special handling for keeping test_data intact
         * 
         *
         * @return boolean true if interaction is ok
         *
         * @todo put extra checking in place for other 802.xx certs that have lab results
         */
        protected function _preDeleteCleanup(){
            

            if (!in_array($this->cert_id, array(
                Certifications::CERT_N_APPROVED,
                Certifications::CERT_N_APPROVED_TEST_ENGINE))) {
                return true;
            }
            //print "\n== processing cert_n for request_id = ".$this->request_id."==\n";
            // check to see if 802.11b exists with in the app
            $rc_cert_b = $this->model()->find('app_id=:app_id and (cert_id=:cert_id1 or cert_id=:cert_id2)',
                    array(  ':app_id'=>$this->app_id,
                            ':cert_id1'=>  Certifications::CERT_B,
                            ':cert_id2'=>  Certifications::CERT_B_TEST_ENGINE,
                        )
                    );

            if (empty($rc_cert_b)){
                $this->addError('cert_id', "unable to find the cert_b to move lab results to");
                //print "-- failed to find the cert_b requestedCertification -- \n";
                return false;
            }

            // change this requested cert from cert_n to cert_b
            //$this->cert_id = $rc_cert_b->cert_id;
            //$rv = $this->save();

            // change 802.11 data
            $test_data = TestData::model()->with('test_fields')
                    ->find("request_id=:request_id and field_name='Is 802.11n Device'",
                            array(":request_id"=>$this->request_id));
            if ($test_data){
                $test_data->data = 'No';
                $rv = $test_data->save();
            }

            
            // delete the original cert_b
            if (!$rv){
                return false;
            }
            
            return $rv;
        }

        /**
         * since certifications abg all share same results we need to move the test_data
         * to another adjacent certification if one is deleted
         *
         * NOTE: for deleting cert_n we require some checks in beforeDelete() to
         * assure that cert_b exists and to prevent the delete if not
         * 
         * @return boolean
         */
        protected function _postDeleteMoveLabResults() {
            //print "_postDeleteMoveLabResults: cert_id={$this->cert_id}, request_id={$this->request_id}";
            if (in_array($this->cert_id, array(
                Certifications::CERT_N_APPROVED,
                Certifications::CERT_N_APPROVED_TEST_ENGINE))) {
                //print "postDelete for cert_n";
                $rc_cert_b = $this->model()->find('app_id=:app_id and (cert_id=:cert_id1 or cert_id=:cert_id2)',
                    array(  ':app_id'=>$this->app_id,
                            ':cert_id1'=>  Certifications::CERT_B,
                            ':cert_id2'=>  Certifications::CERT_B_TEST_ENGINE,
                        )
                    );

                $rc_cert_b->request_id = $this->request_id;
                $rc_cert_b->data_fields_cert_id = $this->cert_id;
                $rc_cert_b->save();
            }
            
        }
        /**
         * copies external data from another RequestedCertifications object to this one
         *  
         * @param object RequestedCertifications
         * @param boolean $force will overwrite existing file if it exists
         * @return boolean true if successful 
         * 
         * @todo make this private or protected and activate it with copyLabData()
         */
        public function copyExternalData($rc_source, $force=false) {
            if (!($rc_source instanceof RequestedCertifications)) {
                throw new InvalidArgumentException("source object is not instance of RequestedCertifications");
            }
	 // refactoring, we need to be more flexable. do a pattern match and don't care if we dont find a match, just copy if found
	  $file_dir = Yii::app()->params->uploaded_data_dir .'/';
	  $filelist = scandir($file_dir);
	  //print "filelist = "; print_r($filelist);
	  foreach ($filelist as $file){
		  $matches = array();
		  $pattern = '/^(\S+)_'.($rc_source->request_id).'\.(xls|pdf|xlsx)/';
		  //print "pattern = $pattern\n";
		  if (preg_match($pattern, $file, $matches)){
			  //print "match found";
			  $src_file = $file_dir . $matches[0];
			  $dest_file = $file_dir . $matches[1] . '_' . ($this->request_id) . '.' . $matches[2];
			  //print "src_file = $src_file, dest_file = $dest_file \n";
			  if (!file_exists($dest_file) || $force) {
				  copy($src_file, $dest_file);
			  }
		  }
	  }
           
	
	
			/*
            $dest_file = $this->getExternalFilePath(); // blank if not exist
            if (!$force && !empty($dest_file)){
                $this->addError('request_id', 'cannot copy over existing external data without setting to "force".');
                return false;
            }
            
            $source_file = $rc_source->getExternalFilePath(); 
            if (empty($source_file)) {
                print "could not find ".($rc_source->getExternalFilePath(true))."\n";
            }
            $dest_file = $this->getExternalFilePath(true); // force the method to return a path even if file not exist
            //print "copy from '$source_file' to '$dest_file'\n";
            $rv = copy($source_file, $dest_file);
            
            if (!$rv) {
                $this->addError('request_id', 'Problem with copying external data from source requested certification.');
            }
            return $rv;
			 * */
			 
        }

	/**
	   * returns path to existing external datafile
	 * @return mixed string path if exists, null if not
	   */
	  public function getExistingExternalDataFilePath() {
		$file_dir = Yii::app()->params->uploaded_data_dir .'/';
		$filelist = scandir($file_dir);
		foreach ($filelist as $file){
			$matches = array();
			$pattern = '/^(\S+)_'.($this->request_id).'\.(xls|pdf|xlsx)/';
			if (preg_match($pattern, $file, $matches)){
				return $file_dir . $matches[0];	
			}
		  }
		  return null;
	}
	  
	  
        /**
         * Required for conditional logic to retrieve the TestResults
         * @return array of TestResults active records
         *
         *
         */
       // public function getTestResultsActiveRecords(){
            //$request_id = (!empty($this->a));
         //   $tr_records = TestResults::model()->findAll('request_id=:request_id', array(':request_id'=>$this->request_id));
           // return $tr_records;
        //}

        /**
         * @param boolean $show_path_if_missing if set to true, you will see the file path even if not exist
         * @return string file path to external data file, blank if it does not exist
         */
        public function getExternalFilePath($show_path_if_missing=false) {
            
            $filebasenames = array();
            switch($this->cert_id){
                case Certifications::CERT_WFD:
                case Certifications::CERT_WFD_TEST_ENGINE:
                    $filebasenames[] = 'wfd_'.$this->request_id.'.xls';
                    break;
                case Certifications::CERT_CWG_RF:
                    $filebasenames[] = 'cwg_rf_'.$this->request_id.'.xls';
                    $filebasenames[] = 'cwg_rf_'.$this->request_id.'.pdf';
                    throw new Exception("not implemented for CWG-RF yet");
                    break;
                
                case Certifications::CERT_WPS2_PIN:
                    $filebasenames[] = 'wps_'.$this->request_id.'.xls';
                    break;
                default:
                    //print "no external data for cert_id={$this->cert_id}\n";
            }
            foreach ($filebasenames as $filename){
                $full_file_path = Yii::app()->params->uploaded_data_dir .'/'. $filename;
                //print "checking file at ...$full_file_path\n";
                if ($show_path_if_missing || file_exists($full_file_path)){
                    return $full_file_path;
                }
            }
            return '';
        }
        
        

        /**
         * returns whether a given certification should have external data by design
         * @param integer $cert_id (optional, if blank uses internal properly cert_id)
         * @return integer EXT_DATA_YES|EXT_DATA_NO|EXT_DATA_MAYBE
         */
        public function shouldHaveExternalDataFile($cert_id=null) {
            if (isset($cert_id) && !preg_match('/^\d+$/', $cert_id)){
                throw new InvalidArgumentException("cert_id is not an integer");
            }
            elseif (isset($cert_id)) {
               // keep the value the same
            }
            else {
                $cert_id = $this->cert_id;   
            }
            
            if (in_array($cert_id, array(
                Certifications::CERT_WFD,
                Certifications::CERT_WFD_TEST_ENGINE,
                Certifications::CERT_WPS2_PIN,
                Certifications::CERT_CWG_RF // note: should not be copied to dependents
                )
            )){
                return self::EXT_DATA_YES;
            }
            
            if (in_array($cert_id, array(
                Certifications::CERT_A,
                Certifications::CERT_A_TEST_ENGINE,
                Certifications::CERT_B,
                Certifications::CERT_B_TEST_ENGINE,
                Certifications::CERT_G,
                Certifications::CERT_G_TEST_ENGINE,
                Certifications::CERT_N,
                Certifications::CERT_N_APPROVED,
                Certifications::CERT_N_APPROVED_TEST_ENGINE
                )
            )){
                //return self::EXT_DATA_MAYBE; // currently we do not save these uploads
                return self::EXT_DATA_NO;
            }

            return self::EXT_DATA_NO;
        }
        
}