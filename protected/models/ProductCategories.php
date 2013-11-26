<?php

/**
 * This is the model class for table "product_categories".
 *
 * The followings are the available columns in table 'product_categories':
 * @property string $category_id
 * @property string $class_id
 * @property string $category
 * @property integer $placement
 */
class ProductCategories extends CActiveRecord
{
    
        /**
         * @var integer category
         * if starting with AP then it is a access point category
         */
        const AP_HOME = 1;
        const AP_ENTERPRISE = 6;
        const AP_GATEWAY = 7;
        const AP_MOBILE = 29;
        
        const STA_INTERNAL_CARD = 2;
        
	/**
	 * Returns the static model of the specified AR class.
	 * @return ProductCategories the static model class
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
		return 'product_categories';
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
			array('class_id', 'length', 'max'=>10),
			array('category', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('category_id, class_id, category, placement', 'safe', 'on'=>'search'),
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
			'category_id' => 'Category',
			'class_id' => 'Class',
			'category' => 'Category',
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

		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('class_id',$this->class_id,true);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('placement',$this->placement);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}