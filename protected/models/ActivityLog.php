<?php

/**
 * This is the model class for table "activity_log".
 *
 * The followings are the available columns in table 'activity_log':
 * @property string $log_id
 * @property string $user_id
 * @property string $table_name
 * @property string $table_id
 * @property string $description
 * @property string $date
 * @property string $old_data
 * @property string $new_data
 */
class ActivityLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ActivityLog the static model class
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
		return 'activity_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, old_data, table_name', 'required'),
			array('user_id, table_id', 'length', 'max'=>10),
			array('table_name', 'length', 'max'=>64),
			array('date, new_data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('log_id, user_id, table_name, table_id, description, date, old_data, new_data', 'safe', 'on'=>'search'),
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
			'log_id' => 'Log',
			'user_id' => 'User',
			'table_name' => 'Table Name',
			'table_id' => 'Table',
			'description' => 'Description',
			'date' => 'Date',
			'old_data' => 'Old Data',
			'new_data' => 'New Data',
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

		$criteria->compare('log_id',$this->log_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('table_name',$this->table_name,true);
		$criteria->compare('table_id',$this->table_id,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('old_data',$this->old_data,true);
		$criteria->compare('new_data',$this->new_data,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}