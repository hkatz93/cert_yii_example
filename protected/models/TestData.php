<?php

/**
 * This is the model class for table "test_data".
 *
 * The followings are the available columns in table 'test_data':
 * @property string $data_id
 * @property string $request_id
 * @property string $field_id
 * @property string $data
 * @property string $posted_on
 * @property string $posted_by
 */
class TestData extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TestData the static model class
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
		return 'test_data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('request_id, field_id, posted_by', 'length', 'max'=>10),
			array('data', 'length', 'max'=>255),
			array('posted_on', 'safe'),
                        array('data', 'validateDataValue'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('data_id, request_id, field_id, posted_on, posted_by', 'safe', 'on'=>'search'),
		);
	}

        /**
         * validate the data based on the restrictions in test_fields
         */
        public function validateDataValue(){
            $tf = TestFields::model()->findByPk($this->field_id);
            $valid_values = explode('/', $tf->format);
            //print "possible data values: "; print_r($valid_values);
            if ($tf->format !='Text' && !in_array($this->data, $valid_values)){
                $msg = '"'.$this->data.'" is not one the valid formats ('.$tf->format.')';
                //print "error msg = $msg";

                $this->addError('data', $msg);
            }
            $in_value = trim($this->data);
            if (strlen($in_value) == 0){
                $this->addError('data', 'data are not allowed to be blank');
            }
        }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		
		return array(
                    // test fields have an additional condition so it will use getTestFields() instead of relation
                    'test_fields' => array(self::BELONGS_TO, 'TestFields', 'field_id'),
                    'requested_certifications' => array(self::BELONGS_TO, 'RequestedCertifications', 'request_id'),
		);
	}
        
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'data_id' => 'Data',
			'request_id' => 'Request',
			'field_id' => 'Field',
			'data' => 'Data',
			'posted_on' => 'Posted On',
			'posted_by' => 'Posted By',
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

		$criteria->compare('data_id',$this->data_id,true);
		$criteria->compare('request_id',$this->request_id,true);
		$criteria->compare('field_id',$this->field_id,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('posted_on',$this->posted_on,true);
		$criteria->compare('posted_by',$this->posted_by,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

        /**
         * returns the relevant data and fields for editing stream data based on app_id
         * 
         * @param integer $app_id
         * @param integer $field_id
         * @return TestData
         */
        public function getTestDataByAppIdAndFieldId($app_id, $field_id){
            
            $connection=Yii::app()->db;   // assuming you have configured a "db" connection
           
            $sql = 'SELECT td.data_id'
                .' FROM requested_certifications as rc '
                .' INNER JOIN test_data as td ON (td.request_id = rc.request_id)'
                .' INNER JOIN test_fields as tf ON (tf.field_id = td.field_id)'
                .' WHERE rc.app_id=:app_id'
                .' AND  tf.field_id=:field_id';

            $command=$connection->createCommand($sql);
            $command->bindParam(":app_id",$app_id,PDO::PARAM_INT);
            $command->bindParam(":field_id",$field_id,PDO::PARAM_INT);
            $rows = $command->queryRow();
            //print_r($rows);
            return TestData::model()->findByPk($rows['data_id']);
        }


        /**
         * helper function, stops a value at the max
         * 
         * @param integer $number
         * @param integer $max_integer
         * @return integer
         */
        public function maxInt($number, $max_integer){
            if (!is_numeric($number)){
                return $number;
            }
            
            if ($number > $max_integer){
                return $max_integer;
            }
            else {
                return $number;
            }
            
            return $integer;
        }
}