<?php

/**
 * This is the model class for table "product_certifications".
 *
 * The followings are the available columns in table 'product_certifications':
 * @property string $cid
 * @property string $cert_id
 *
 * The followings are the available model relations:
 */
class ProductCertifications extends AuditedActiveRecord
{
    /**
     * if set to true prohibits a delete() from affecting other tables
     * @var boolean 
     */
    protected $_non_cascading_delete = false;

    /**
	 * Returns the static model of the specified AR class.
	 * @return ProductCertifications the static model class
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
		return 'product_certifications';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cid', 'length', 'max'=>16),
			array('cert_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('cid, cert_id', 'safe', 'on'=>'search'),
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
                    'certifications' => array(self::BELONGS_TO, 'Certifications', 'cert_id'),
                    'products'=>array(self::HAS_ONE, 'Products', '', 'on'=>'cid=cid', 'joinType'=>'INNER JOIN', 'alias'=>'products')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'cid' => 'CID',
			'cert_id' => 'cert_id',
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

		$criteria->compare('cid',$this->cid,true);
		$criteria->compare('cert_id',$this->cert_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

        /**
         * for the case of temporarily removing publicly visible certifications
         * such as when an application gets pushed back from 7: COMPLETED
         * we don't wish to have a delete that removes certifications
         * from the application or anywhere else
         * @return boolean
         */
        public function nonCascadingDelete(){
            $this->_non_cascading_delete = true;
            return $this->delete();
        }

        /**
         *
         * @return boolean if not true, delete will not execute
         */
        public function  beforeDelete() {
            parent::beforeDelete();

            // no before delete processing for flag _non_cascading_delete == true
            if ($this->_non_cascading_delete){
                return true;
            }
            $connection=Yii::app()->db;   // assuming you have configured a "db" connection
            
            // prevent cert_n deletions since they need special handling
            $is_cert_n = (in_array($this->cert_id,
                    array(
                        Certifications::CERT_N,
                        Certifications::CERT_N_APPROVED,
                        Certifications::CERT_N_APPROVED_TEST_ENGINE))) ? true : false;
            
            if ($is_cert_n){
                // get all of the RequestedCertifications and delete them
                $prod = $this->products;
                // get the database rows that ultimately will be deleted
                $sql = "SELECT request_id from certification_applications as ca
                    INNER JOIN requested_certifications as rc ON (rc.app_id = ca.app_id)
                    WHERE product_id=:product_id and rc.cert_id=:cert_id";

                $command=$connection->createCommand($sql);
                $command->bindParam(":product_id",$prod->product_id,PDO::PARAM_INT);
                $command->bindParam(":cert_id",$this->cert_id,PDO::PARAM_INT);

                $rows = $command->queryAll();
                //print "deleting these: ";print_r($rows); print "\n\n";

                foreach ($rows as $row){
                    $rc = RequestedCertifications::model()->find('request_id=:request_id', array(':request_id'=>$row['request_id']));
                    $rv = $rc->delete();
                    if ($rv == false){
                        Yii::log("unable to delete RequestedCertifications (request_id=".$row['request_id'].")");
                        return false;
                    }
                }
                return true;
            }
            else {
                
                //print_r($connection);

                //$product_id = $this->products->product_id; // very slow, just use SQL instead

                $product = Products::model()->find('cid=:cid', array(':cid'=>$this->cid));
                $product_id = $product->product_id;
                $cert_id = $this->cert_id;
                //Yii::log("[product_id = $product_id ]");
                if ($product_id && $cert_id) {
                    $sql = " DELETE rc, tr, td"
                            ." FROM certification_applications as ca"
                            ." INNER JOIN requested_certifications as rc ON (ca.app_id = rc.app_id)"
                            ." LEFT JOIN test_results as tr ON (tr.request_id = rc.request_id)"
                            ." LEFT JOIN test_data as td ON (td.request_id = tr.request_id)"
                            ." INNER JOIN products as p ON (p.product_id = ca.product_id)"
                            ." LEFT JOIN product_certifications as pc ON (p.cid = pc.cid and pc.cert_id = rc.cert_id)"
                            ." WHERE ca.product_id=$product_id and rc.cert_id=$cert_id";
                            //." WHERE ca.product_id=:product_id";

                    $command=$connection->createCommand($sql);
                    //$command->bindParam(":product_id",$model->product_id,PDO::PARAM_INT);
                    //$command->bindParam(":cert_id",$username,PDO::PARAM_INT);
                    $rv = $command->execute();

                    if ($rv){
                        Yii::log("[deleted $rv rows]");
                        return true;
                    }
                    else {
                        Yii::log("problem with beforeDelete for ProductCertifications model");

                        return false; // this will halt the deletion
                    }
                }
                else {
                    Yii::log("possible database integrity issue? We should have had value for product_id ($product_id) and cert_id ($cert_id)");
                }
                return true;
            }
        }

        /**
         * make sure to insert as a passed test in the latest publishable cert app
         * @return boolean if not true then insert is halted
         */
        public function  beforeSave() {
            parent::beforeSave();
            //return true;
            
            // stop duplicates from going in 
            // the database stops this but this is another layer of protection
            $pc = $this->find('cert_id=:cert_id and cid=:cid', array('cert_id'=>$this->cert_id, 'cid'=>$this->cid));
            if ($pc){
                $this->addError('cert_id', 'unable to add duplicate cert');
                return false;
            }

            //print "cid = ".$this->cid;
            $prod_id = Products::cid2ProductId($this->cid);
            //print "prod_id = ".$prod_id;
            //return true;
            //
            // prevent adding certification if no publishable application exists
            $ca = CertificationApplications::model()
                    ->most_recent()
                    ->publishable()
                    ->find('product_id=:product_id', array('product_id'=>$prod_id));
            
            if (!($ca instanceof CertificationApplications)) {
                //print "ca is not instanceof CertificationApplications";
                $this->addError('cert_id', "Unable to find publishable application; cannot add certification to it.");
                return false;
            }
            // check to make sure the requested certification for the application does not exist
            $rc = RequestedCertifications::model()
                    ->find('app_id=:app_id and cert_id=:cert_id',
                            array('app_id'=>$ca->app_id, 'cert_id'=>$this->cert_id));
            if (!$rc){
                $rv = $ca->addCertification($this->cert_id, true);
            }
            else {
                $rv = true;
            }
            if (!$rv){
                //print "ca addCertification failed...\n";
                $this->addError('cert_id', "Unable to add certification (cert_id={$this->cert_id}) to application:" . (print_r($ca->getErrors(), true)));
                return false;
            }
            return true;
        }
}