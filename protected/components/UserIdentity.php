<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    private $_id;
    private $_username;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
            if (!isset($_SESSION)){
                session_start();
                if ($_SESSION['opentoken']['access_cert'] == 1){
                    $this->errorCode=self::ERROR_NONE;
                    $this->_id = $_SESSION['user_id'];
                    /**
                     * @todo look into ways to use role based permissions rather than default to admin user
                     */
                    $user = Users::model()->findByPk($this->_id);
                    if ($user->isSuperAdmin()){
                        $this->_username = 'admin';
                    }
                    else {
                        $this->_username = $_SESSION['original_username'];
                    }
                    //if (strtoupper($_SESSION['user_type']) == 'ADMIN'){
                    //    $this->username = 'admin';
                    //    $this->_username = 'admin';
                   // }
                   
                }
                else {
                    $this->errorCode=self::ERROR_USERNAME_INVALID;
                }
            }
            if (0){
		$users=array(
			// username => password
			'demo'=>'demo',
			'admin'=>'adminC12',
		);
		if(!isset($users[$this->username]))
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if($users[$this->username]!==$this->password)
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
			$this->errorCode=self::ERROR_NONE;


            }
		return !$this->errorCode;
	}

        public function getId()
        {
          //  return parent::getId();
            return $this->_id;
        }
        public function getName()
        {
          //  return parent::getName();
            return $this->_username;
        }

}