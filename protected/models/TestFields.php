<?php

/**
 * This is the model class for table "test_fields".
 *
 * The followings are the available columns in table 'test_fields':
 * @property string $field_id
 * @property string $cert_id
 * @property string $test_type
 * @property string $field_name
 * @property string $format
 * @property string $placement
 * @property string $test_plan
 * if (in_array($k, array(7849, 7918, 7874, 7946)))  { // if one of the fields is External Registrar Support (WPS2 only)
 */
class TestFields extends CActiveRecord
{
    /**
     * @var integer Maximum Tested Tx Spatial Streams (Station and Draft n)
     */ 
    const STA_MAX_TX_STREAMS_CERT_N = 6664;
    /**
     * @var integer Maximum Tested Rx Spatial Streams (Station and Draft n)
     */
    const STA_MAX_RX_STREAMS_CERT_N = 6665;
    /**
     * @var integer Maximum Tested Tx Spatial Streams (Access Point and Draft n)
     */
    const AP_MAX_TX_STREAMS_CERT_N = 6666;
    /**
     * @var integer Maximum Tested Rx Spatial Streams (Access Point and Draft n)
     */
    const AP_MAX_RX_STREAMS_CERT_N = 6667;
    /**
     * @var integer Maximum Tested Tx Spatial Streams in 2.4 GHz (Access Point and cert n)
     */
    const AP_MAX_TX_STREAMS_24_CERT_N_APPROVED = 6674;
    /**
     * @var integer Maximum Tested Rx Spatial Streams in 2.4 GHz (Access Point and cert n)
     */
    const AP_MAX_RX_STREAMS_24_CERT_N_APPROVED = 6675;
    /**
     * @var integer Maximum Tested Tx Spatial Streams in 5.0 GHz (Access Point and cert n)
     */
    const AP_MAX_TX_STREAMS_50_CERT_N_APPROVED = 6676;
    /**
     * @var integer Maximum Tested Rx Spatial Streams in 5.0 GHz (Access Point and cert n)
     */
    const AP_MAX_RX_STREAMS_50_CERT_N_APPROVED = 6677;
    /**
     * @var integer Maximum Tested Tx Spatial Streams in 2.4 GHz (Access Point and cert n test engine)
     */
    const AP_MAX_TX_STREAMS_24_CERT_N_APPROVED_TEST_ENGINE = 6953;
    /**
     * @var integer Maximum Tested Rx Spatial Streams in 2.4 GHz (Access Point and cert n test engine)
     */
    const AP_MAX_RX_STREAMS_24_CERT_N_APPROVED_TEST_ENGINE = 6954;
    /**
     * @var integer Maximum Tested Tx Spatial Streams in 5.0 GHz (Access Point and cert n test engine)
     */
    const AP_MAX_TX_STREAMS_50_CERT_N_APPROVED_TEST_ENGINE = 6955;
    /**
     * @var integer Maximum Tested Rx Spatial Streams in 5.0 GHz (Access Point and cert n test engine)
     */
    const AP_MAX_RX_STREAMS_50_CERT_N_APPROVED_TEST_ENGINE = 6956;
    /**
     * @var integer Maximum Tested Tx Spatial Streams in 2.4 GHz (Station & cert n)
     */
    const STA_MAX_TX_STREAMS_24_CERT_N_APPROVED = 7232;
    /**
     * @var integer Maximum Tested Rx Spatial Streams in 2.4 GHz (Station & cert n)
     */
    const STA_MAX_RX_STREAMS_24_CERT_N_APPROVED = 7233; 
    /**
     * @var integer Maximum Tested Tx Spatial Streams in 5.0 GHz (Station & cert n)
     */
    const STA_MAX_TX_STREAMS_50_CERT_N_APPROVED = 7234;
    /**
     * @var integer Maximum Tested Rx Spatial Streams in 5.0 GHz (Station & cert n)
     */
    const STA_MAX_RX_STREAMS_50_CERT_N_APPROVED = 7235;
    /**
     * @var integer Maximum Tested Tx Spatial Streams in 2.4 GHz (Station & cert n test engine)
     */
    const STA_MAX_TX_STREAMS_24_CERT_N_APPROVED_TEST_ENGINE = 7517;
    /**
     * @var integer Maximum Tested Rx Spatial Streams in 2.4 GHz (Station & cert n test engine)
     */
    const STA_MAX_RX_STREAMS_24_CERT_N_APPROVED_TEST_ENGINE = 7518;
    /**
     * @var integer Maximum Tested Tx Spatial Streams in 5.0 GHz (Station & cert n test engine)
     */
    const STA_MAX_TX_STREAMS_50_CERT_N_APPROVED_TEST_ENGINE = 7519;
    /**
     * @var integer Maximum Tested Rx Spatial Streams in 5.0 GHz (Station & cert n test engine)
     */
    const STA_MAX_RX_STREAMS_50_CERT_N_APPROVED_TEST_ENGINE = 7520;
    /**
     * @var integer Internal Registrar Support (Station)
     */
     const AP_INTERNAL_REGISTRAR_CERT_WPS2_PIN = 7848;
    /**
     * @var integer External Registrar Support (Access Point)
     */
     const AP_EXTERNAL_REGISTRAR_CERT_WPS2_PIN = 7849;
    /**
     * @var integer Internal Registrar Support (Access Point)
     */
     const AP_INTERNAL_REGISTRAR_CERT_WPS_PIN = 7873;
    /**
     * @var integer External Registrar Support (Access Point)
     */
     const AP_EXTERNAL_REGISTRAR_CERT_WPS_PIN = 7874;
    /**
     * @var integer External Registrar Support (Station)
     */
     const STA_EXTERNAL_REGISTRAR_CERT_WPS2_PIN = 7918;
    /**
     * @var integer External Registrar Support (Station)
     */
     const STA_EXTERNAL_REGISTRAR_CERT_WPS_PIN = 7946;

        /**
	 * Returns the static model of the specified AR class.
	 * @return TestFields the static model class
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
		return 'test_fields';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cert_id, placement', 'length', 'max'=>10),
			array('test_type', 'length', 'max'=>12),
			array('field_name', 'length', 'max'=>255),
			array('format', 'length', 'max'=>111),
			array('test_plan', 'length', 'max'=>28),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('field_id, cert_id, test_type, field_name, format, placement, test_plan', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'field_id' => 'Field',
			'cert_id' => 'Cert',
			'test_type' => 'Test Type',
			'field_name' => 'Field Name',
			'format' => 'Format',
			'placement' => 'Placement',
			'test_plan' => 'Test Plan',
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

		$criteria->compare('field_id',$this->field_id,true);
		$criteria->compare('cert_id',$this->cert_id,true);
		$criteria->compare('test_type',$this->test_type,true);
		$criteria->compare('field_name',$this->field_name,true);
		$criteria->compare('format',$this->format,true);
		$criteria->compare('placement',$this->placement,true);
		$criteria->compare('test_plan',$this->test_plan,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

        /**
         * prevent any deletes
         * @return boolean false always to prevent all deletes
         */
        public function beforeDelete(){
            parent::beforeDelete();
            return false;
        }

        /**
         * prevent any updates
         * @return boolean false always to prevent all updates and inserts
         */
        public function beforeSave(){
            parent::beforeSave();
            return false;
        }

        /**
         * used to assure that the test_fields table still has these fields
         * @return array of integers
         */
        public function getConstantFieldIds() {
            return array(
                self::STA_MAX_TX_STREAMS_CERT_N,
                self::STA_MAX_RX_STREAMS_CERT_N,
                self::AP_MAX_TX_STREAMS_CERT_N,
                self::AP_MAX_RX_STREAMS_CERT_N,
                self::AP_MAX_TX_STREAMS_24_CERT_N_APPROVED,
                self::AP_MAX_RX_STREAMS_24_CERT_N_APPROVED,
                self::AP_MAX_TX_STREAMS_50_CERT_N_APPROVED,
                self::AP_MAX_RX_STREAMS_50_CERT_N_APPROVED,
                self::AP_MAX_TX_STREAMS_24_CERT_N_APPROVED_TEST_ENGINE,
                self::AP_MAX_RX_STREAMS_24_CERT_N_APPROVED_TEST_ENGINE,
                self::AP_MAX_TX_STREAMS_50_CERT_N_APPROVED_TEST_ENGINE,
                self::AP_MAX_RX_STREAMS_50_CERT_N_APPROVED_TEST_ENGINE,
                self::STA_MAX_TX_STREAMS_24_CERT_N_APPROVED,
                self::STA_MAX_RX_STREAMS_24_CERT_N_APPROVED,
                self::STA_MAX_TX_STREAMS_50_CERT_N_APPROVED,
                self::STA_MAX_RX_STREAMS_50_CERT_N_APPROVED,
                self::STA_MAX_TX_STREAMS_24_CERT_N_APPROVED_TEST_ENGINE,
                self::STA_MAX_RX_STREAMS_24_CERT_N_APPROVED_TEST_ENGINE,
                self::STA_MAX_TX_STREAMS_50_CERT_N_APPROVED_TEST_ENGINE,
                self::STA_MAX_RX_STREAMS_50_CERT_N_APPROVED_TEST_ENGINE,
                self::AP_INTERNAL_REGISTRAR_CERT_WPS2_PIN,
                self::AP_EXTERNAL_REGISTRAR_CERT_WPS2_PIN,
                self::AP_INTERNAL_REGISTRAR_CERT_WPS_PIN,
                self::AP_EXTERNAL_REGISTRAR_CERT_WPS_PIN,
                self::STA_EXTERNAL_REGISTRAR_CERT_WPS2_PIN,
                self::STA_EXTERNAL_REGISTRAR_CERT_WPS_PIN,
            );
        }

}