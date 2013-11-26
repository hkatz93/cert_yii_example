<?php

/**
 * This is the model class for table "certifications".
 *
 * The followings are the available columns in table 'certifications':
 * @property string $cert_id
 * @property string $description
 * @property string $display_name
 * @property string $var_name
 * @property string $category_id
 * @property integer $placement
 * @property integer $test_engine
 * @property string $correspondent
 * @property string $pricebookentryid_std
 * @property string $price_std
 * @property string $pricebookentryid_online
 *
 * The followings are the available model relations:
 * @property object $categories
 * @property array $requested_certifications
 */
class Certifications extends CActiveRecord
{
    const TEST_ENGINE_TRUE = 1;
    const TEST_ENGINE_FALSE = 0;

    // List of constants directly copied from certifications table for ease of programming
    /**
     * @var integer IEEE 802.11b
     */
    const CERT_B = 1;
    /**
     * @var integer IEEE 802.11a
     */
    const CERT_A = 2;
    /**
     * @var integer IEEE 802.11g
     */
    const CERT_G = 3;
    /**
     * @var integer WPA&#153; - Enterprise
     */
    const CERT_WPA_E = 4;
    /**
     * @var integer WPA&#153; - Personal
     */
    const CERT_WPA_P = 5;
    /**
     * @var integer WPA2&#153; - Enterprise
     */
    const CERT_WPA2_E = 6;
    /**
     * @var integer WPA2&#153; - Personal
     */
    const CERT_WPA2_P = 7;
    /**
     * @var integer WMM&reg;
     */
    const CERT_WMM = 8;
    /**
     * @var integer IEEE 802.11d
     */
    const CERT_D = 9;
    /**
     * @var integer IEEE 802.11h
     */
    const CERT_H = 10;
    /**
     * @var integer EAP-TLS
     */
    const CERT_EAP11 = 11;
    /**
     * @var integer EAP-TTLS/MSCHAPv2
     */
    const CERT_EAP12 = 12;
    /**
     * @var integer PEAPv0/EAP-MSCHAPv2
     */
    const CERT_EAP13 = 13;
    /**
     * @var integer PEAPv1/EAP-GTC
     */
    const CERT_EAP14 = 14;
    /**
     * @var integer EAP-SIM
     */
    const CERT_EAP15 = 15;
    /**
     * @var integer EAP-TLS
     */
    const CERT_EAP16 = 16;
    /**
     * @var integer EAP-TTLS/MSCHAPv2
     */
    const CERT_EAP17 = 17;
    /**
     * @var integer PEAPv0/EAP-MSCHAPv2
     */
    const CERT_EAP18 = 18;
    /**
     * @var integer PEAPv1/EAP-GTC
     */
    const CERT_EAP19 = 19;
    /**
     * @var integer EAP-SIM
     */
    const CERT_EAP20 = 20;
    /**
     * @var integer WMM Power Save
     */
    const CERT_WMMPS = 21;
    /**
     * @var integer CWG-RF
     */
    const CERT_CWG_RF = 22;
    /**
     * @var integer Wi-Fi Protected Setup - PIN
     */
    const CERT_WPS_PIN = 23;
    /**
     * @var integer Wi-Fi Protected Setup - PBC
     */
    const CERT_WPS_PBC = 24;
    /**
     * @var integer IEEE 802.11b (Test Engine)
     */
    const CERT_B_TEST_ENGINE = 25;
    /**
     * @var integer IEEE 802.11a (Test Engine)
     */
    const CERT_A_TEST_ENGINE = 26;
    /**
     * @var integer IEEE 802.11g (Test Engine)
     */
    const CERT_G_TEST_ENGINE = 27;
    /**
     * @var integer WPA&#153; - Personal (Test Engine)
     */
    const CERT_WPA_P_TEST_ENGINE = 28;
    /**
     * @var integer WPA2&#153; - Personal (Test Engine)
     */
    const CERT_WPA2_P_TEST_ENGINE = 29;
    /**
     * @var integer WPA&#153; - Enterprise (Test Engine)
     */
    const CERT_WPA_E_TEST_ENGINE = 30;
    /**
     * @var integer WPA2&#153; - Enterprise (Test Engine)
     */
    const CERT_WPA2_E_TEST_ENGINE = 31;
    /**
     * @var integer WMM&reg; (Test Engine)
     */
    const CERT_WMM_TEST_ENGINE = 32;
    /**
     * @var integer IEEE 802.11n draft 2.0
     */
    const CERT_N = 33;
    /**
     * @var integer WMM Power Save (Test Engine)
     */
    const CERT_WMMPS_TEST_ENGINE = 34;
    /**
     * @var integer Wi-Fi Protected Setup - NFC
     */
    const CERT_WPS_NFC = 35;
    /**
     * @var integer Voice - Personal
     */
    const CERT_VOICE_P = 36;
    /**
     * @var integer EAP-AKA
     */
    const CERT_EAP37 = 37;
    /**
     * @var integer EAP-FAST
     */
    const CERT_EAP38 = 38;
    /**
     * @var integer EAP-AKA
     */
    const CERT_EAP39 = 39;
    /**
     * @var integer EAP-FAST
     */
    const CERT_EAP40 = 40;
    /**
     * @var integer IEEE 802.11n
     */
    const CERT_N_APPROVED = 41;
    /**
     * @var integer IEEE 802.11n (Test Engine)
     */
    const CERT_N_APPROVED_TEST_ENGINE = 42;
    /**
     * @var integer Short Guard Interval
     */
    const CERT_N_SGI20 = 43;
    /**
     * @var integer Greenfield Preamble
     */
    const CERT_N_GREEN_PRE = 44;
    /**
     * @var integer TX A-MPDU
     */
    const CERT_N_TXAMPDU = 45;
    /**
     * @var integer STBC
     */
    const CERT_N_STBC = 46;
    /**
     * @var integer 40 MHz operation in 2.4 GHz, with coexistence mechanisms
     */
    const CERT_N_40_IN_24 = 47;
    /**
     * @var integer 40 MHz operation in 5 GHz
     */
    const CERT_N_40_IN_50 = 48;
    /**
     * @var integer HT Duplicate (MCS 32)
     */
    const CERT_N_MCS32 = 49;
    /**
     * @var integer Wi-Fi Direct&trade;
     */
    const CERT_WFD = 50;
    /**
     * @var integer Wi-Fi Direct&trade; (Test Engine)
     */
    const CERT_WFD_TEST_ENGINE = 51;
    /**
     * @var integer Wi-Fi Protected Setup - PIN
     */
    const CERT_WPS2_PIN = 52;
    /**
     * @var integer Wi-Fi Protected Setup - PBC
     */
    const CERT_WPS2_PBC = 53;
   
    /**
     * @var integer WMM&reg;-Admission Control (Test Engine)
     */
    const CERT_WMMAC = 54;
    /**
     * @var integer TDLS
     */
    const CERT_TDLS = 56;
    /**
     * @var integer TDLS (Test Engine)
     */
    const CERT_TDLS_TEST_ENGINE = 57;
    /**
     * @var integer PMF 
     */
    const CERT_PMF = 58;
    /**
     * @var integer PMF (Test Engine)
     */
    const CERT_PMF_TEST_ENGINE = 59;
    /**
     * @var integer IBSS with Wi-Fi Protected Setup
     */
    const CERT_IBSS = 60;
    /**
     * @var integer Voice - Enterprise 
     */
    const CERT_VOICE_E = 61;
    /**
     * @var integer Voice - Enterprise (Test Engine)
     */
    const CERT_VOICE_E_TEST_ENGINE = 62;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Certifications the static model class
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
		return 'certifications';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description', 'required'),
			array('placement, test_engine', 'numerical', 'integerOnly'=>true),
			array('display_name', 'length', 'max'=>64),
			array('var_name', 'length', 'max'=>16),
			array('category_id, correspondent, price_std', 'length', 'max'=>10),
			array('pricebookentryid_std, pricebookentryid_online', 'length', 'max'=>18),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('cert_id, description, display_name, var_name, category_id, placement, test_engine, correspondent, pricebookentryid_std, price_std, pricebookentryid_online', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
           
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'category_classes' => array(self::BELONGS_TO, 'CategoryClasses', 'category_id'),
                    //'requested_certifications' => array(self::HAS_MANY, 'RequestedCertifications', 'cert_id', 'index'=>'cert_id')
                    'requested_certifications' => array(self::HAS_MANY, 'RequestedCertifications', 'cert_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'cert_id' => 'Cert',
			'description' => 'Description',
			'display_name' => 'Display Name',
			'var_name' => 'Var Name',
			'category_id' => 'Category',
			'placement' => 'Placement',
			'test_engine' => 'Test Engine',
			'correspondent' => 'Correspondent',
			'pricebookentryid_std' => 'Pricebookentryid Std',
			'price_std' => 'Price Std',
			'pricebookentryid_online' => 'Pricebookentryid Online',
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

		$criteria->compare('cert_id',$this->cert_id,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('display_name',$this->display_name,true);
		$criteria->compare('var_name',$this->var_name,true);
		//$criteria->compare('category_id',$this->category_id,true);
		//$criteria->compare('placement',$this->placement);
		$criteria->compare('test_engine',$this->test_engine);
		//$criteria->compare('correspondent',$this->correspondent,true);
		//$criteria->compare('pricebookentryid_std',$this->pricebookentryid_std,true);
		//$criteria->compare('price_std',$this->price_std,true);
		//$criteria->compare('pricebookentryid_online',$this->pricebookentryid_online,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	* @return array of possible test engine types
	*/
	public function getTestEngineTypeOptions() {
            return array(self::TEST_ENGINE_TRUE, self::TEST_ENGINE_FALSE);
        }

        //public function getCertificationCategories(){
          //  $rows = $this->categories;
            //return $rows;
            
        //}

        /**
         * return list of all cert_ids
         * mostly here to assure constants are kept in sync with certifications table
         * @return array of all the cert_id (should exactly match table)
         */
        public function getCertIdList(){
	    
	    // hard coding this is no longer a good idea since it needs 
	    // to be manually updated
	    
	    
	    $c_obj_list = Certifications::model()->findAll();
	    $list = array();
	    foreach ($c_obj_list as $cert){
		$list[] = $cert['cert_id'];
	    }
	    return $list;
	    /*
            return array(
                self::CERT_B,
                self::CERT_A,
                self::CERT_G,
                self::CERT_WPA_E,
                self::CERT_WPA_P,
                self::CERT_WPA2_E,
                self::CERT_WPA2_P,
                self::CERT_WMM,
                self::CERT_D,
                self::CERT_H,
                self::CERT_EAP11,
                self::CERT_EAP12,
                self::CERT_EAP13,
                self::CERT_EAP14,
                self::CERT_EAP15,
                self::CERT_EAP16,
                self::CERT_EAP17,
                self::CERT_EAP18,
                self::CERT_EAP19,
                self::CERT_EAP20,
                self::CERT_WMMPS,
                self::CERT_CWG_RF,
                self::CERT_WPS_PIN,
                self::CERT_WPS_PBC,
                self::CERT_B_TEST_ENGINE,
                self::CERT_A_TEST_ENGINE,
                self::CERT_G_TEST_ENGINE,
                self::CERT_WPA_P_TEST_ENGINE,
                self::CERT_WPA2_P_TEST_ENGINE,
                self::CERT_WPA_E_TEST_ENGINE,
                self::CERT_WPA2_E_TEST_ENGINE,
                self::CERT_WMM_TEST_ENGINE,
                self::CERT_N,
                self::CERT_WMMPS_TEST_ENGINE,
                self::CERT_WPS_NFC,
                self::CERT_VOICE_P,
                self::CERT_EAP37,
                self::CERT_EAP38,
                self::CERT_EAP39,
                self::CERT_EAP40,
                self::CERT_N_APPROVED,
                self::CERT_N_APPROVED_TEST_ENGINE,
                self::CERT_N_SGI20,
                self::CERT_N_GREEN_PRE,
                self::CERT_N_TXAMPDU,
                self::CERT_N_STBC,
                self::CERT_N_40_IN_24,
                self::CERT_N_40_IN_50,
                self::CERT_N_MCS32,
                self::CERT_WFD,
                self::CERT_WFD_TEST_ENGINE,
                self::CERT_WPS2_PIN,
                self::CERT_WPS2_PBC,
		self::CERT_WMMAC,
		self::CERT_TDLS,
		self::CERT_TDLS_TEST_ENGINE,
		self::CERT_PMF,
		self::CERT_PMF_TEST_ENGINE,
		self::CERT_IBSS
		    );
	     * 
	     */
            
        }
        
        /**
         * prevent record deletion
         * @return boolean false 
         */
        public function  beforeDelete() {
            parent::beforeDelete();
            $this->addError('cert_id','certifications are read-only');
            return false;
        }

        /**
         * prevent changing information
         * @return boolean false
         */
        public function beforeSave(){
            parent::beforeSave();
            $this->addError('cert_id','certifications are read-only');
            return false;
        }

        /**
         * returns list of cert_id, application must have one of these to be considered cert n
         * @return array of integers
         */
        public function getCertNRequiredList() {
            return array(
                //self::CERT_N, // ignore Draft N for now
                self::CERT_N_APPROVED,
                self::CERT_N_APPROVED_TEST_ENGINE,
                
            );
        }

        /**
         * returns list of cert_id, application cannot have one of these unless
         * the product already has one from getCertNRequiredList()
         * (Draft N does not permit you to add these, I believe)
         * 
         * @return array of integers
         */
        public function getCertNOptionalList() {
            return array(
                self::CERT_N_40_IN_24,
                self::CERT_N_40_IN_50,
                self::CERT_N_GREEN_PRE,
                self::CERT_N_MCS32,
                self::CERT_N_SGI20,
                self::CERT_N_STBC,
                self::CERT_N_TXAMPDU
            );
        }

        /**
         * returns the mapping so you can determine the non-test engine equivalent of a test engine cert_id
         * @return array key=>value [test engine cert id]=>[non-test engine cert_id]
         */
        public function nonTestEngineToTestEngineMapArray(){
            $sql = 'SELECT t1.cert_id as cert_id_test_engine, t2.cert_id as cert_id_non_test_engine '
                .' FROM certifications as t1 '
                .' INNER JOIN certifications as t2 '
                .'   ON (t1.cert_id != t2.cert_id AND t1.var_name = t2.var_name)'
                .' WHERE t1.test_engine=1 AND t2.test_engine=0   ';

            $connection=Yii::app()->db;
            $command=$connection->createCommand($sql);
            //$command->bindParam(":app_id",$this->app_id,PDO::PARAM_INT);

            $rows = $command->queryAll();
            $map = array();
            foreach ($rows as $r){
                $map[$r['cert_id_test_engine']] = $r['cert_id_non_test_engine'];
            }
            return $map;
        }

        /**
         *
         * @param array $config
         * @return array suitable for populating a CHtml::dropDown
         */
        public function getDropDownArray($config = array()){

            $allowed_config_keys = array('is_test_engine');
            // is_test_engine: value = NULL, all certs
            // is_test_engine: value = 0, all cert minus certs for non test engine
            // is_test_engine: value = 1, all cert minus certs for test engine

            $keys = array_keys($config);
            //if ()
            $ary_diff = array_diff($keys, $allowed_config_keys);
            if (count($ary_diff)){
                $not_allowed_keys = array_keys($ary_diff);
                throw new InvalidArgumentException("the following options key values are not allowed:".(implode(',',$not_allowed_keys)));
            }

            if (isset($config['is_test_engine']) && $config['is_test_engine'] == 1){
                $map_ary = self::nonTestEngineToTestEngineMapArray();
                $remove_cert_id_list = $map_ary;
            }
            
            if (isset($config['is_test_engine']) && $config['is_test_engine'] == 0){
                $map_ary = self::nonTestEngineToTestEngineMapArray();
                $remove_cert_id_list = array_keys($map_ary);
            }
        
            $sql = 'SELECT c.cert_id, c.display_name, cc.category, c.placement as cert_placement, cc.placement as cat_placement'
                .' FROM certifications as c'
                .' INNER JOIN certification_categories as cc ON (cc.category_id = c.category_id)'
                . ((!empty($remove_cert_id_list)) ? 'WHERE c.cert_id NOT IN ('.implode(',', $remove_cert_id_list).')' : '')
                .' ORDER BY cc.placement, c.placement';


            $connection=Yii::app()->db;
            $command=$connection->createCommand($sql);
            //$command->bindParam(":app_id",$this->app_id,PDO::PARAM_INT);

            $rows = $command->queryAll();

            // build the array
            $option_ary = array();
            $i = 0;
            foreach ($rows as $row){
                $option_ary[$i]['id'] = $row['cert_id'];
                $option_ary[$i]['text'] = $row['display_name'];
                $option_ary[$i]['group'] = $row['category'];
                $i++;
            }
            //print_r($option_ary);
            return $option_ary;
        }
    
}