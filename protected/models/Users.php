<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property string $user_id
 * @property string $uid
 * @property string $contact_uid
 * @property string $parent_id
 * @property string $fname
 * @property string $mi
 * @property string $lname
 * @property string $email
 * @property string $phone
 * @property string $username
 * @property string $password
 * @property string $type
 * @property integer $permissions
 * @property string $status
 * @property integer $beta
 * @property integer $reset_password
 * @property integer $receive_notifications
 * @property integer $submit_applications
 * @property string $last_login
 *
 * The followings are the available model relations:
 *
 * @property array certification_applications
 * @property object labs
 * @property object companies
 */
class Users extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Users the static model class
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
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid', 'required'),
			array('permissions, beta, reset_password, receive_notifications, submit_applications', 'numerical', 'integerOnly'=>true),
			array('uid, contact_uid', 'length', 'max'=>20),
			array('parent_id', 'length', 'max'=>10),
			array('fname, lname, phone', 'length', 'max'=>32),
			array('mi', 'length', 'max'=>1),
			array('email', 'length', 'max'=>128),
			array('username, password', 'length', 'max'=>64),
			array('type', 'length', 'max'=>9),
			array('status', 'length', 'max'=>8),
			array('last_login', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, uid, cotact_uid, parent_id, fname, mi, lname, email, phone, username, password, type, permissions, status, beta, reset_password, receive_notifications, submit_applications, last_login', 'safe', 'on'=>'search'),
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
                    'certification_applications'=> array(self::HAS_MANY, 'CertificationApplications', 'requested_by'),
                    'labs'=> array(self::BELONGS_TO, 'Labs', 'parent_id'),
                    'companies'=> array(self::BELONGS_TO, 'Companies', 'parent_id')
                    

		);
	}

        /**
         * returns true if the user is a super admin
         * @return boolean
         */
        public function isSuperAdmin() {
            return (($this->type === 'Admin') ? true : false);
        }
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'User',
			'uid' => 'uid',
			'contact_uid' => 'Contact uid',
			'parent_id' => 'Parent',
			'fname' => 'First Name',
			'mi' => 'M',
			'lname' => 'Last Name',
			'email' => 'Email',
			'phone' => 'Phone',
			'username' => 'Username',
			'password' => 'Password',
			'type' => 'Type',
			'permissions' => 'Permissions',
			'status' => 'Status',
			'beta' => 'Beta',
			'reset_password' => 'Reset Password',
			'receive_notifications' => 'Receive Notifications',
			'submit_applications' => 'Submit Applications',
			'last_login' => 'Last Login',
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('uid',$this->uid,true);
		$criteria->compare('contact_uid',$this->contact_uid,true);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('fname',$this->fname,true);
		$criteria->compare('mi',$this->mi,true);
		$criteria->compare('lname',$this->lname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('username',$this->username,true);
		//$criteria->compare('password',$this->password,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('permissions',$this->permissions);
		$criteria->compare('status',$this->status);
		//$criteria->compare('beta',$this->beta);
		$criteria->compare('reset_password',$this->reset_password);
		$criteria->compare('receive_notifications',$this->receive_notifications);
		$criteria->compare('submit_applications',$this->submit_applications);
		$criteria->compare('last_login',$this->last_login,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}