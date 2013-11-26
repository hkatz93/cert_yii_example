<?php

/**
 * This is the model class for table "companies".
 *
 * The followings are the available columns in table 'companies':
 * @property string $company_id
 * @property string $oid
 * @property string $company_name
 * @property string $company_name_short
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $country
 * @property string $url
 * @property string $logo_id
 * @property string $affiliate_parent
 * @property integer $status
 * @property integer $delete_me
 *
 * The followings are the available model relations:
 */
class Companies extends CActiveRecord
{

        /**
         * @var integer company is active
         */
        const ACTIVE = 17;

        /**
         * @var integer company is active
         */
        const INACTIVE = 20;
        
        /**
         * @var integer the company_id of Wi-Fi Alliance
         */
        const COMPANY_ID_WFA = 894;
        
	/**
	 * Returns the static model of the specified AR class.
	 * @return Companies the static model class
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
		return 'companies';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, delete_me', 'numerical', 'integerOnly'=>true),
			array('oid', 'length', 'max'=>20),
			array('company_name, address1, address2', 'length', 'max'=>128),
			array('company_name_short, city, state', 'length', 'max'=>64),
			array('zipcode', 'length', 'max'=>16),
			array('country', 'length', 'max'=>3),
			array('url', 'length', 'max'=>255),
			array('logo_id, affiliate_parent', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('company_id, oid, company_name, company_name_short, address1, address2, city, state, zipcode, country, url, logo_id, affiliate_parent, status, delete_me', 'safe', 'on'=>'search'),
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
			'company_id' => 'Company',
			'oid' => 'Oid',
			'company_name' => 'Company Name',
			'company_name_short' => 'Company Name Short',
			'address1' => 'Address1',
			'address2' => 'Address2',
			'city' => 'City',
			'state' => 'State',
			'zipcode' => 'Zipcode',
			'country' => 'Country',
			'url' => 'Url',
			'logo_id' => 'Logo',
			'affiliate_parent' => 'Affiliate Parent',
			'status' => 'Status',
			'delete_me' => 'Delete Me',
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

		$criteria->compare('company_id',$this->company_id);
		$criteria->compare('oid',$this->oid,true);
		$criteria->compare('company_name',$this->company_name,true);
		$criteria->compare('company_name_short',$this->company_name_short,true);
		$criteria->compare('address1',$this->address1,true);
		$criteria->compare('address2',$this->address2,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('logo_id',$this->logo_id,true);
		$criteria->compare('affiliate_parent',$this->affiliate_parent,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('delete_me',$this->delete_me);

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

        /**
         * prevent updates since companies are edited by soap service
         * that does not yet use this model
         * @return boolean false
         */
        public function  beforeSave() {
            parent::beforeSave();
            return false;
        }

        public function scopes() {
            return array(
                'active'=>array(
                    'condition'=>'status='. self::ACTIVE
                        . " AND oid !='\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0' ",
                    'order'=>'company_name ASC',
                ),
                'inactive'=>array(
                    'condition'=>'status='. self::INACTIVE,
                    'order'=>'company_name ASC',
                ),
                'active_plus_current'=>array(
                    'condition'=>'(status='. self::ACTIVE
                        . " AND oid !='\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0' )"
                        . " OR (company_id=".((!empty($this->company_id)) ? $this->company_id : 0).")",
                    'order'=>'company_name ASC',
                ),
                
            );
        }

        

}