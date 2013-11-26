<?php

/**
* @covers ProductCertifications
*/
class ProductCertificationsTest extends CDbTestCase {

    public $fixtures=array(
    'products'=>'Products',
    'certification_applications'=>'CertificationApplications',
    'product_certifications'=>'ProductCertifications',
    'requested_certifications'=>'RequestedCertifications',
    'test_data'=>'TestData',
    );

    public function testRelationsCertifications(){
        $prod_cert = $this->product_certifications('0'); // from fixture, which is database since it resets it on load
        // lazy load
        $cert = $prod_cert->certifications;
        $this->assertTrue($cert instanceof Certifications, "is not instance of Certifications");
    }

    public function testRelationsProducts() {
        $prod_cert = $this->product_certifications('0'); // from fixture, which is database since it resets it on load
        // lazy load
        $prod = $prod_cert->products;
        $this->assertTrue($prod instanceof Products, "is not instance of Products");
    }

    
    /**
     * assure that proper cleanup happens after product_certifications row is deleted
     */
    public function testDelete() {
        
        $prod_cert = $this->product_certifications('0'); // from fixture, which is database since it resets it on load
        
        // grab information from requested certifications so we can query for it later to confirm delete occured

        $prod = $prod_cert->products;

        $connection=Yii::app()->db;   // assuming you have configured a "db" connection
        //
        // get the database rows that ultimately will be deleted
        $sql = "SELECT * from certification_applications as ca
            INNER JOIN requested_certifications as rc ON (rc.app_id = ca.app_id)
            WHERE product_id=:product_id and rc.cert_id=:cert_id";

        $command=$connection->createCommand($sql);
        $product_id = $prod->product_id;
        $cert_id = $prod_cert->cert_id;
        $command->bindParam(":product_id",$product_id,PDO::PARAM_INT);
        $command->bindParam(":cert_id", $cert_id,PDO::PARAM_INT);

        $rows = $command->queryAll();
        //print_r($rows);
        $this->assertTrue(!empty($rows), " rows are empty");
        
        /* this ultimately was not correct since the link was incorrect to requested_certifications
        $this->assertTrue($prod instanceof Products, " is not an instanceof Products");
        $apps = $prod->certification_applications;
        $this->assertTrue(is_array($apps), " is not an array");
        $this->assertTrue($apps[0] instanceof CertificationApplications, " is not an instanceof CertificationApplications");
        $req_certs = $apps[0]->requested_certifications;
        $this->assertTrue(is_array($req_certs), " is not an array");
        $this->assertTrue($req_certs[0] instanceof RequestedCertifications, " is not an instanceof RequestedCertifications");
        $request_id = $req_certs[0]->request_id;
        $this->assertTrue(is_numeric($request_id), " is not numeric");
        */

        // grab rows



        // or with eager load
        /* hmm not working as expected
        $cert_id = $prod_cert->cert_id;
        $criteria=new CDbCriteria;
        $criteria->with=array(
            'products',
            'products.certification_applications',
            'certification_applications.requested_certifications',
        );
        $criteria->condition = 'product_certifications.cert_id=$cert_id';
        $r = ProductCertifications::model()->findAll($criteria);
        */
        
        //$prod = $prod->certification_applications;

        $rv =$prod_cert->delete();
        $this->assertTrue($rv, "failed to delete");

        $rows = $command->queryAll();
        //print "------\n";
        //print_r($rows);
        $this->assertTrue(empty($rows), " rows should be empty");
    }

    public function testDeleteCertN() {
        
        $prod_cert = ProductCertifications::model()->find('cert_id=:cert_id', array(':cert_id'=>Certifications::CERT_N_APPROVED));
        $this->assertTrue($prod_cert instanceof ProductCertifications, "should have found one with 802.11n");

        $prod = $prod_cert->products;
        $this->assertTrue($prod instanceof Products, " not instance of Products");
        
        $connection=Yii::app()->db;   // assuming you have configured a "db" connection
        //
        // get the database rows that ultimately will NOT be deleted
        $sql = "SELECT request_id from certification_applications as ca
            INNER JOIN requested_certifications as rc ON (rc.app_id = ca.app_id)
            WHERE product_id=:product_id and rc.cert_id=:cert_id";

        $command=$connection->createCommand($sql);
        $command->bindParam(":product_id",$prod->product_id,PDO::PARAM_INT);
        $command->bindParam(":cert_id",$prod_cert->cert_id,PDO::PARAM_INT);

        $rows = $command->queryAll();
        //print_r($rows);
        $this->assertTrue(!empty($rows), " rows are empty, when they should not be");


        $rv = $prod_cert->delete();
        //print "var_dump =";var_dump($rv);
        $this->assertTrue($rv, "should not have prevented 802.11n from being deleted");

        $rows = $command->queryAll();
        //print_r($rows);
        $this->assertTrue(empty($rows), " rows are not empty, when they should be");

        $prod_cert = ProductCertifications::model()->find('cert_id=:cert_id', array(':cert_id'=>Certifications::CERT_N_APPROVED));
        $this->assertFalse($prod_cert instanceof ProductCertifications, "should not have found one with 802.11n");


    }


    public function testDeleteCertBWhenHasNoLabData(){
        // find a product that has cert_n
        $prod = $this->products('0');
        $has_cert_n = false;
        $product_certifications = $prod->getProductCertifications();
        foreach ($product_certifications as $pc) {
            if ($pc->cert_id == Certifications::CERT_N_APPROVED) {
                $has_cert_n = true;
            }
        }
        $this->assertTrue($has_cert_n, "product does not have cert n");
        // delete cert_b
        $has_cert_b = false;
        $product_certifications = $prod->getProductCertifications();
        foreach ($product_certifications as $pc) {
            if ($pc->cert_id == Certifications::CERT_B) {
                $has_cert_b = true;
                //$this->assertEquals($pc)
                $this->assertTrue($pc->delete(), "should have allowed cert b to be deleted");
            }
        }
        $this->assertTrue($has_cert_b, "could not find cert_b in product to be deleted");
    }
    
    /**
     * prevent deleting 802.11abg, when the requested certification has lab data
     * @depends testDeleteCertN
     */
    public function testDeleteCertBPreventionWhenHasLabData(){

        // find a product that has cert_n
        $prod = $this->products('0');
        $has_cert_n = false;
        $product_certifications = $prod->getProductCertifications();
        foreach ($product_certifications as $pc) {
            if ($pc->cert_id == Certifications::CERT_N_APPROVED) {
                $has_cert_n = true;
                $this->assertTrue($pc->delete(),"should have allowed cert_n to be deleted");
            }
        }
        $this->assertTrue($has_cert_n, "product does not have cert n");


        // delete cert_b
        $has_cert_b = false;
        $product_certifications = $prod->getProductCertifications();
        foreach ($product_certifications as $pc) {
            if ($pc->cert_id == Certifications::CERT_B) {
                $has_cert_b = true;
                $apps = $prod->certification_applications;
                $rc = RequestedCertifications::model()
                    ->find('cert_id=:cert_id and app_id=:app_id',
                            array('cert_id'=>Certifications::CERT_B, 'app_id'=>$apps[0]->app_id));

                $this->assertGreaterThan(1, count($rc->test_data), "cert_b should have had test data");
                $this->assertTrue($pc->delete(), "should not have allowed cert b to be deleted");
            }
        }
        $this->assertTrue($has_cert_b, "could not find cert_b in product to be deleted");
    }


    
     /**
     * assure that proper cleanup DOES NOT happen after product_certifications row is deleted
     * @depends testDelete
     */
    public function testNonCascadingDelete() {

        $prod_cert = $this->product_certifications('0'); // from fixture, which is database since it resets it on load

        // grab information from requested certifications so we can query for it later to confirm delete occured

        $prod = $prod_cert->products;

        $connection=Yii::app()->db;   // assuming you have configured a "db" connection
        //
        // get the database rows that ultimately will be deleted
        $sql = "SELECT * from certification_applications as ca
            INNER JOIN requested_certifications as rc ON (rc.app_id = ca.app_id)
            WHERE product_id=:product_id and rc.cert_id=:cert_id";

        $command=$connection->createCommand($sql);
        $command->bindParam(":product_id",$prod->product_id,PDO::PARAM_INT);
        $command->bindParam(":cert_id",$prod_cert->cert_id,PDO::PARAM_INT);

        $rows = $command->queryAll();
        //print_r($rows);
        $this->assertTrue(!empty($rows), " rows are empty");

        $rv =$prod_cert->nonCascadingDelete();
        $this->assertTrue($rv, "failed to delete");

        $rows = $command->queryAll();
        //print "------\n";
        //print_r($rows);
        $this->assertTrue(!empty($rows), " rows should not be empty");

    }

}
?>
