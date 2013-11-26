<?php

/**
 * This is the model class for table "category_classes".
 *
 * The followings are the available columns in table 'category_classes':
 * @property string $id
 * @property string $class
 * @property integer $placement
 *
 * The followings are the available model relations:
 */
class CategoryClasses extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CategoryClasses the static model class
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
		return 'category_classes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('placement', 'numerical', 'integerOnly'=>true),
			array('class', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, class, placement', 'safe', 'on'=>'search'),
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
                    'certifications' => array(self::HAS_MANY, 'Certifications', 'category_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'class' => 'Class',
			'placement' => 'Placement',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('class',$this->class,true);
		$criteria->compare('placement',$this->placement);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
        /**
         * prevent record deletion
         * @return boolean false
         */
        public function  beforeDelete() {
            parent::beforeDelete();
            return false;
        }
}