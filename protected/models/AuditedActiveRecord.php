<?php

/**
 * This is the model class for an abstract class to monitor table changes
 * Simply extend this and automatically get auditing for an active record.
 *
 * The followings are the available model relations:
 */
abstract class AuditedActiveRecord extends CActiveRecord
{

        /**
         * @var string possible value of description column
         */
        const DESCRIPTION_EDIT = 'Edit';

        /**
         * @var string possible value of description column
         */
        const DESCRIPTION_DELETE = 'Delete';

        /**
         * @var array associative key-value array of pre-save database values
         */
        private $old_data_ary = array();

        /**
         * @var array associative key-value array of post-save database values
         */
        private $new_data_ary = array();

        /**
         * @var boolean if set to false, no changes will be tracked
         */
	protected $trackChanges = true;

        /**
         * use to override default table name
         * @var string used to populate table_name in activity_log table
         */
        protected $activity_log_table_name;

        /**
         * calculate which data in the active record has changed from the row in database
         * @return boolean false if no change detected
         */
	final protected function _calculateAuditRowChange(){
            $change_detected = false;
            if (is_array($this->primaryKey)){
                throw new Exception("does not work with composite primary keys");
            }
            // get currrent value in DB
            $db_ar = $this->model()->findByPk($this->primaryKey);
            foreach ($db_ar->attributes as $key=>$old_value){
                // compare old data to new data
                if ($old_value != $this->$key){
                    $change_detected = true;
                    $this->old_data_ary[$key]=$old_value;
                    $this->new_data_ary[$key]=$this->$key;

                }
            }
            $rv = $this->_addAdditionalData();
            $change_detected = ($rv || $change_detected) ? true : false;
            return $change_detected;
        }



        /**
         * some custom recording of changes are needed for app or product
         * @return void
         */
        protected function _addAdditionalData() {
            
            $table_name = $this->getActivityLogTableName();
            //print __FUNCTION__."::table_name = $table_name";
            if (in_array($table_name, array('applications', 'certification_applications'))){
                //print "populating app_id";
                $this->old_data_ary['app_id'] = $this->primaryKey;
                $this->new_data_ary['app_id'] = $this->primaryKey;

                // add variable certifications if it did not exist before
                if (!isset($this->old_data_ary['certifications'])){
                    $this->old_data_ary['certifications'] = array();
                }
                if (!isset($this->new_data_ary['certifications'])){
                    $this->new_data_ary['certifications'] = array();
                }
            }
            if (in_array($table_name, array('products'))){
                $ca = CertificationApplications::model()->most_recent()->publishable()->find('product_id=:product_id', array('product_id'=>$this->product_id));
                $this->old_data_ary['app_id'] = $ca->app_id;
                $this->new_data_ary['app_id'] = $ca->app_id;

                // add variable certifications if it did not exist before
                if (!isset($this->old_data_ary['certifications'])){
                    $this->old_data_ary['certifications'] = array();
                }
                if (!isset($this->new_data_ary['certifications'])){
                    $this->new_data_ary['certifications'] = array();
                }
            }
        }

        /**
         * allow custom manipulation of properties old_data_ary and new_data_ary before saving
         * typically this is used for recording the certifications for Products and CertificationApplications models
         * 
         * @return boolean false if process failed, this would stop the recording of the activity
         */
        protected function beforeSaveActivityLog(){
            return true;
        }

        /**
         * allow custom manipulation of properties old_data_ary and new_data_ary before saving
         * typically this is used for recording the certifications for Products and CertificationApplications models
         *
         * @return boolean false if process failed, this typically will not prevent the transaction since it is after delete completed
         */
        protected function afterDeleteActivityLog(){
            return true;
        }

        /**
         * accessor function to modify $old_data and $new_data
         * 
         * @param <type> $old_data
         * @param <type> $new_data
         */
       // public function addTrackedChanges($old_data, $new_data){
            // don't allow overwriting of normally tracked attributes

            
        //}
        
        /**
         * @return boolean should always be true here
         */
        public function beforeSave(){
            parent::beforeSave();
            
            // do nothing if trackChanges is false
            if ($this->trackChanges === false) {
                return true;
            }

            if ($this->isNewRecord){
                return true; // no change audit needed if new
            }
            if (!$this->beforeSaveActivityLog()) {
                return false;
            }
            

            $rv = true;
            if($this->_calculateAuditRowChange()) {
                $al = new ActivityLog;
                // solution below suggested from the following URL:
                // http://www.yiiframework.com/forum/index.php?/topic/20976-console-app-and-webuser/
                //
                if (Yii::app() instanceof CConsoleApplication) {
                    $default_username = Yii::app()->params->default_admin_username; // defined in config/main.php
                    //print "default user name = $default_username\n";
                    $user = Users::model()->find('username=:username', array('username'=>$default_username));
                    $yii_user_id = $user->user_id;
                    //print "yii_user_id = $yii_user_id\n";
                }
                else {
                    $yii_user_id = Yii::app()->user->getId();
                }
                
                $al->user_id = (!empty($yii_user_id)) ? $yii_user_id : 0;
                $al->table_name = $this->getActivityLogTableName();
                $al->table_id = $this->primaryKey; // primary key value
                $al->description = self::DESCRIPTION_EDIT;
                $al->date = new CDbExpression('NOW()');
                $al->old_data = serialize($this->old_data_ary);
                $al->new_data = serialize($this->new_data_ary);
                $rv = $al->save();
                if(!$rv){
                    $this->addError('log_id', 'unable to save audit data');
                }
            }
            //$al
            return $rv;

        }

        /**
         * allows overriding the default table name to use for activity_log table
         * set the activity_log_table_name property in the child class to override the default
         * 
         * NOTE: using this override this is generally not desirable,
         * but it is here to help compatibility with legacy code specifically when table_name='application'
         * which is not a real table
         * 
         * @return string table name to use in the table_name column of activity_log 
         */
        public function getActivityLogTableName() {
            if (!empty($this->activity_log_table_name)){
                return $this->activity_log_table_name;
            }
            else {
                return $this->tableName();
            }
        }

        /**
         * @return boolean false
         */
        public function  afterDelete() {
            parent::afterDelete();
            
            // do nothing if trackChanges is false
            if ($this->trackChanges === false) {
                return true;
            }
            
            //print "attributes for ".$this->tableName()." = ";
            //print_r($this->attributes );
            //return true;
            //foreach ($this->attributes as $att){
            //    $this->old_data_ary = array($att => $this->$att);
            //}
            //print "afterDelete(), primary key = ".$this->primaryKey."\n";
            //print "afterDelete(), attributes = ".(print_r($this->attributes, true))."\n";
            $al = new ActivityLog;
            $al->user_id = 0;
            $al->table_name = $this->getActivityLogTableName();
            $al->table_id = $this->primaryKey; // primary key value
            $al->description = self::DESCRIPTION_DELETE;
            $al->date = new CDbExpression('NOW()');
            $al->old_data = serialize($this->attributes);
            //$al->new_data = serialize($this->new_data_ary);
            $rv = $al->save();
            if (!$rv){
                //print "unable to save delete log. Errors = ".(print_r($this->getErrors(), true));
                //print "\n";
            }
            // find
            $this->afterDeleteActivityLog();
            return true;
        }

        /**
         *
         * @param integer $pk optional primary key value
         * @return array
         */
        public function getActivityLogRows($pk=null){
            $pk = (!empty($pk)) ? $pk : $this->primaryKey;
            //$table_name = (!empty($table_name)) ? $table_name : $this->tableName();
            $table_name = $this->getActivityLogTableName();

            //print __FUNCTION__ .":pk = $pk, table_name = $table_name";
            if (is_array($pk)){
                throw new InvalidArgumentException("does not work with composite primary keys");
            }
            $sql = 'SELECT log_id, u.user_id, u.username, table_name, table_id, description, date, old_data, new_data '
                    .' FROM activity_log as al '
                    .' LEFT JOIN users as u ON (u.user_id = al.user_id) '
                    .' WHERE table_name=:table_name AND table_id=:table_id'
                    .' ORDER BY date DESC';

            //$sql = "SELECT log_id, user_id, table_name, table_id, description, date, old_data, new_data "
            //        ." FROM activity_log where table_name='$table_name' and table_id=$pk";

            //print "SQL = $sql\n\n";
            $connection=Yii::app()->db;   // assuming you have configured a "db" connection
            $command=$connection->createCommand($sql);
            $command->bindParam(":table_name",$table_name,PDO::PARAM_STR);
            $command->bindParam(":table_id",$pk,PDO::PARAM_INT);

            $rows =  $command->queryAll();
            //print "rows = ".(print_r($rows, true));
            return $rows;
        }
}