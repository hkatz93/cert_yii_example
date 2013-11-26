<?php

/**
 * This is the model class for table "test_results".
 *
 * The followings are the available columns in table 'test_results':
 * @property integer $result_id
 * @property integer $request_id
 * @property integer $import_result_id
 * @property integer $result
 * @property string $posted_on
 * @property string $posted_by 
 *
 * The followings are the available model relations:
 * @property object $requested_certifications
 */
class TestResults extends CActiveRecord
{
        /**
         * @var integer test result passed
         */
        const PASS = 13;
        /**
         * @var integer test result failed
         */
        const FAIL = 14;
        /**
         * @var integer test result not tested
         */
        const NOT_TESTED = 21;

	/**
	 * Returns the static model of the specified AR class.
	 * @return TestResults the static model class
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
		return 'test_results';
	}

	/**
	 * @return array validation rules for model attributes.
         * @uses TestResults::validateResult(), TestResults::validateRequestId()
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('result', 'validateResult'),
                        array('request_id', 'validateRequestId'),
                        array('import_result_id', 'validateImportResultId'),
			array('posted_by', 'length', 'max'=>10),
			array('posted_on', 'default',
                            'value'=>new CDbExpression('NOW()'), // automatically set date on insert
                            'setOnEmpty'=>false,'on'=>'insert'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('result_id, request_id, result, posted_on, posted_by', 'safe', 'on'=>'search'),
		);
	}

        public function validateResult($attribute, $params){
            if (!in_array($this->result, array(TestResults::PASS, TestResults::FAIL, TestResults::NOT_TESTED))){
                 $this->addError('result','must be PASS, FAIL, or NOT TESTED');
            }
        }
        
        public function validateRequestId($attribute, $params){
            $rc = RequestedCertifications::model()->findByPk($this->request_id);
            if (empty($rc->request_id)){
                 $this->addError('request_id','request_id does not exist in the requested certifications');
            }
        }
        
	public function validateImportResultId($attribute, $params){
            $rc = RequestedCertifications::model()->findByPk($this->request_id);
            if (!empty($this->import_result_id)){
		$tr = TestResults::model()->findByPk($this->import_result_id);
		if (!($tr instanceof TestResults)) {
			$this->addError('import_result_id','import_result_id does not match any existing in the test results ids');
		}
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
                    'requested_certifications' => array(self::BELONGS_TO, 'RequestedCertifications', 'request_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'result_id' => 'Result',
			'request_id' => 'Request',
			'result' => 'Result',
			'posted_on' => 'Posted On',
			'posted_by' => 'Posted By',
		);
	}

        /**
         * @return array suitable for a CHtml::dropDownList
         */
        public function resultsDropDownArray(){
            return array(self::PASS=>'Pass', self::FAIL=>'Fail', self::NOT_TESTED=>"Not Tested");
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

		$criteria->compare('result_id',$this->result_id,true);
		$criteria->compare('request_id',$this->request_id,true);
		$criteria->compare('result',$this->result);
		$criteria->compare('posted_on',$this->posted_on,true);
		$criteria->compare('posted_by',$this->posted_by,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
        /**
         * prohibit deletion if there is an associated requested_certifications
         *
         * @return boolean is not true it will prevent a delete
         */
        public function beforeDelete() {
            parent::beforeDelete();
            $rc = $this->requested_certifications;
            if (!empty($rc->request_id)){
                $this->addError('request_id', "Not permitted to delete if there is an associated requested certification");
                return false;
            }
            else {
                return true;
            }
        }

        /**
         * assure that the user is populated
         * @return boolean if not true then active record will not save()
         *
         * @todo insert the default user or the authenticated user id into posted_by
         */
        public function beforeSave(){
            parent::beforeSave();
            
            if (empty($this->posted_by)) {
                // needs posted_by to equal the id for the user
            }
            return true;
        }
}