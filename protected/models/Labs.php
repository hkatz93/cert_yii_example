<?php

/**
 * This is the model class for table "labs".
 *
 * The followings are the available columns in table 'labs':
 * @property string $lab_id
 * @property string $oid
 * @property string $company_name
 * @property string $address
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $country
 * @property string $url
 * @property string $overview
 * @property string $terms
 * @property string $contact_name
 * @property string $contact_email
 * @property string $contact_phone
 * @property string $location
 * @property string $logo_id
 * @property string $status
 * @property string $reports_status
 *
 * The followings are the available model relations:
 * @property array $applications
 */
class Labs extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Labs the static model class
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
		return 'labs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('oid', 'length', 'max'=>20),
			array('company_name, address1, address2, contact_email', 'length', 'max'=>128),
			array('city, state, contact_name, location', 'length', 'max'=>64),
			array('zipcode', 'length', 'max'=>16),
			array('country', 'length', 'max'=>3),
			array('url', 'length', 'max'=>255),
			array('contact_phone', 'length', 'max'=>32),
			array('logo_id', 'length', 'max'=>10),
			array('status, reports_status', 'length', 'max'=>8),
			array('address, overview, terms', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('lab_id, oid, company_name, address, address1, address2, city, state, zipcode, country, url, overview, terms, contact_name, contact_email, contact_phone, location, logo_id, status, reports_status', 'safe', 'on'=>'search'),
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
			'applications' => array(self::HAS_MANY, 'CertificationApplications', 'certifying_lab')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'lab_id' => 'Lab',
			'oid' => 'Oid',
			'company_name' => 'Company Name',
			'address' => 'Address',
			'address1' => 'Address1',
			'address2' => 'Address2',
			'city' => 'City',
			'state' => 'State',
			'zipcode' => 'Zipcode',
			'country' => 'Country',
			'url' => 'Url',
			'overview' => 'Overview',
			'terms' => 'Terms',
			'contact_name' => 'Contact Name',
			'contact_email' => 'Contact Email',
			'contact_phone' => 'Contact Phone',
			'location' => 'Location',
			'logo_id' => 'Logo',
			'status' => 'Status',
			'reports_status' => 'Reports Status',
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

		$criteria->compare('lab_id',$this->lab_id,true);
		$criteria->compare('oid',$this->oid,true);
		$criteria->compare('company_name',$this->company_name,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('address1',$this->address1,true);
		$criteria->compare('address2',$this->address2,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('overview',$this->overview,true);
		$criteria->compare('terms',$this->terms,true);
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('contact_email',$this->contact_email,true);
		$criteria->compare('contact_phone',$this->contact_phone,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('logo_id',$this->logo_id,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('reports_status',$this->reports_status,true);
                
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