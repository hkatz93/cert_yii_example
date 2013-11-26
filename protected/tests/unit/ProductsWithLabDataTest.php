<?php

/**
* @covers Products
 *
 * created this separate case to mimimize the re-populating the "large" table
 * test_data
*/
class ProductsWithLabDataTest extends CDbTestCase {

    public $fixtures=array(
    'products'=>'Products',
    'certification_applications'=>'CertificationApplications',
    'product_certifications'=>'ProductCertifications',
    'requested_certifications'=>'RequestedCertifications',
    'activity_log'=>'ActivityLog',
    'test_results'=>'TestResults',
    'test_data'=>'TestData',
    );

    public function testCertificationsArray() {
        $p = $this->products('0');
        $rows = $p->certificationsArray();
        $this->assertInternalType('array', $rows);
        $this->assertGreaterThan(0, count($rows));
        $required_flds = array('category', 'cert_id', 'display_name', 'product_certification_id');
        foreach ($required_flds as $fld){
            $this->assertTrue(isset($rows[0][$fld]), "could not find field $fld in the row");
        }

        // need to compare this list to the contents of
        $prod_certs = ProductCertifications::model()->with('certifications')->findAll("cid=:cid", array(':cid'=>$p->cid));
        $this->assertEquals(count($prod_certs), count($rows));

    }
    
    /**
     *
     * @depends testCertificationsArray
     */
    public function testAddCertification(){
        $prod = $this->products('0');
        $cert_ary = $prod->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertFalse(in_array(Certifications::CERT_VOICE_P, $cert_id_list), "should not have found cert in list");
        $rv = $prod->addCertification(Certifications::CERT_VOICE_P);
        $this->assertTrue($rv, " unable to add certification");

        $cert_ary = $prod->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertTrue(in_array(Certifications::CERT_VOICE_P, $cert_id_list), "should have found cert in list");
    }

    /**
     * @depends testAddCertification
     */
    public function testDeleteCertification(){
        $prod = $this->products('0');
        $rv = $prod->addCertification(Certifications::CERT_VOICE_P);
        $cert_ary = $prod->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertTrue(in_array(Certifications::CERT_VOICE_P, $cert_id_list), "should have found cert in list");
        
        $rv = $prod->deleteCertification(Certifications::CERT_VOICE_P);
        $this->assertTrue($rv, "not able to delete certification");

        $cert_ary = $prod->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertFalse(in_array(Certifications::CERT_VOICE_P, $cert_id_list), "should not have found cert in list");
    }

    /**
     * @depends testDeleteCertification
     */
    public function testDeleteCertN(){
        $prod = $this->products('0');
        // make sure cert n exists
        $cert_ary = $prod->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertTrue(in_array(Certifications::CERT_N_APPROVED, $cert_id_list), "should have found cert in list");

        $rv = $prod->deleteCertification(Certifications::CERT_N_APPROVED);
        $this->assertTrue($rv, "unable to delete cert n");

        $cert_ary = $prod->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertFalse(in_array(Certifications::CERT_N_APPROVED, $cert_id_list), "should have deleted cert_n from product");
    }
    
    /**
     * @depends testCertificationsArray
     * @depends testAddCertification
     * @depends testDeleteCertification
     */
    public function testSetCertifications(){
        $prod = $this->products('0');
        
        $set_id_list = array();
        $set_id_list[] = Certifications::CERT_B;
        $set_id_list[] = Certifications::CERT_A;
        $set_id_list[] = Certifications::CERT_G;
        $set_id_list[] = Certifications::CERT_VOICE_P;

        $cert_ary = $prod->certificationsArray();
        $this->assertNotEquals(count($cert_ary), count($set_id_list), "counts of certifications should not match the count of the input list");

        $rv = $prod->setCertifications($set_id_list);
        $this->assertTrue($rv, "unable to set certifications");

        $cert_ary = $prod->certificationsArray();
        $this->assertEquals(count($cert_ary), count($set_id_list), "counts of certifications match the count of the input list");

        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }

        $diff_1 = array_diff($cert_id_list, $set_id_list);
        $diff_2 = array_diff($set_id_list, $cert_id_list);

        $this->assertEquals(count($diff_1), 0, "should be identical arrays");
        $this->assertEquals(count($diff_2), 0, "should be identical arrays");
    }   
    
    /**
     * parent products (or modules) sometimes get additional certifications
     * that need to cascade down to the dependent 
     * 
     * NOTE: due to how data is restored at the BEGINING of each test
     * please do not put this at end of test suite since it affects 
     * data that needs to be retored for other tests
     * 
     */
    public function testAppendParentApplicationResults() {
        //$this->markTestIncomplete(
        //  'This test has not been implemented yet.'
        //);
        
        $parent_prod = $this->products('0');
        $dep_prod = $this->products('1');
        
        // add certification to parent
        $parent_prod->addCertification(Certifications::CERT_CWG_RF);
        
        // fake it, we will alter the data of $dep product
        // so it will be a dependent product with a publishable application
        $ca_list = $dep_prod->certification_applications;
        $ca = (is_array($ca_list)) ? $ca_list[0] : $ca_list;
        $this->assertTrue($ca instanceof CertificationApplications, "dep app not located");
        
        // make sure the dependent product app is publishable
        $ca->hold = 0;
        $ca->publish_on ='Deferred Date';
        $ca->status = CertificationApplications::STATUS_COMPLETE;
        $ca->deferred_date = '2000-01-01'; // before today
        $this->assertTrue($ca->save(), "unable to save changes to dependent product app");
        $dep_prod->parent_id = $parent_prod->product_id;
        $this->assertTrue($dep_prod->save(), "could not make dep prod a child product");
        
        
        // more fake data, assure that passing test result exists
        // for each of the requested_certifications
        $rc_list = $ca->requested_certifications;
        foreach ($rc_list as $rc) {
            $tr = new TestResults();
            $tr->request_id = $rc->request_id;
            $tr->posted_by = 7828;
            $tr->result = TestResults::PASS;
            $this->assertTrue($tr->save(), "not able to save TestResults");
        }
        
        
        
        
        // lets find a cert_id that exists in parent but not in dependent product
        //
        $p_certs = $parent_prod->certIdList();
        $d_certs = $ca->certIdList();
        
        // assure that CWG-RF does not exist in the pre-existing dependent certs
        
        
        $cert_diff = array_diff($p_certs, $d_certs);
        sort($cert_diff);
        //print "cert_diff = "; print_r($cert_diff);
        $this->assertTrue(count($cert_diff) > 0, "not able to find certs in parent that do not exist in dependent");
        
        // select the cert_id to add to import from parent
        $cert_id = $cert_diff[0];
        $cert_id = Certifications::CERT_N_APPROVED; // just picking a cert_id that has lab data
        
        $this->assertTrue((!empty($cert_id) && is_numeric($cert_id)), "cert_id is not numeric");
        //print "cert_id to add = $cert_id \n";
        
        //$cert_id = $cert_list[0]['cert_id'];
        $rc = $parent_prod->getMostRecentPublishableRequestedCertificationByCertId($cert_id);
        $this->assertTrue($rc instanceof RequestedCertifications, "is not instance of RequestedCertifications");
        $parent_app_id = $rc->app_id;
        //print "parent_app_id = $parent_app_id \n";
        
        // attempt the parent application import
        $rv = $dep_prod->appendParentApplicationResults($parent_app_id);
        //print "errors = "; print_r($dep_prod->errors);
        $this->assertTrue($rv, "could not import parent app:");
        
        // test to see that the data now exists in the dependent product
        // check the published certifications for the dependent product
        //
        
        // make sure that dependent application now has same certs as parent application
        $p_app = CertificationApplications::model()->findByPk($rc->app_id);
        $d_app = CertificationApplications::model()->findByPk($ca->app_id);
        
        $p_certs = $p_app->certIdList();
        $d_certs = $d_app->certIdList();
        
        $this->assertTrue(count($p_certs)>0, "parent certs should be > 0");
        $this->assertTrue(count($d_certs)>0, "dependent certs should be > 0");
        $diff1 = array_diff($p_certs, $d_certs); 
        
        // remove CWG-RF from the certifications list that should have been imported
        foreach ($diff1 as $index =>$value){
            if ($value == Certifications::CERT_CWG_RF){
                unset($diff1[$index]);
            }
        }
        
        $this->assertEquals(count($diff1), 0, "cert_ids that should have been imported but were not: ".implode(',', $diff1));
        
        // assure that CWG-RF is NOT imported
        $this->assertFalse(in_array(Certifications::CERT_CWG_RF, $d_certs), "CWG-RF should not have been imported");
        
        // check the dependent applications requested certifications 
        // to see if there are test results
        
        $d_rc_list = $d_app->requested_certifications;
        foreach ($d_rc_list as $d_rc) {
            $this->assertTrue($d_rc->test_results instanceof TestResults, 
                    "dependent requested certification (request_id={$d_rc->request_id}, cert_id={$d_rc->cert_id}) did not have associated test result");
            
            // check to see if the test data was copied for CERT_N_APPROVED
            if ($d_rc->cert_id == Certifications::CERT_N_APPROVED){
                $d_td = $d_rc->test_data;
                $this->assertTrue(is_array($d_td), "test data did not return an array");
                $this->assertTrue($d_td[0] instanceof TestData, "did not return test data for requested certification (request_id={$d_rc->request_id} cert_id={$d_rc->cert_id})");
            }
        }
        
        
        // assure that the dependent product has all of the certifications of the parent application
        // these certifications should be published
        //
        $d_prod = Products::model()->findByPk($d_app->product_id);
        $d_certs = $d_prod->certIdList();
        $this->assertTrue(count($d_certs)>0, "published dependent certs should be > 0");
        
        $diff2 = array_diff($d_certs, $p_certs);
        $this->assertEquals(count($diff2), 0, "cert_ids that should have been imported as published cert but were not: ".implode(',', $diff2));
    }
    

	
    /**
     * @depends testCertificationsArray
     * @depends testAddCertification
     * @depends testDeleteCertification
     */
    public function testSetCertificationsWithNoPublishedCerts(){
        $prod = $this->products('1');
        $this->assertTrue($prod instanceof Products, "prod is not a Products instance");
        $this->assertEquals(count($prod->certificationsArray()), 0, "this product should not have any certs yet");

        $set_id_list = array();
        $set_id_list[] = Certifications::CERT_B;
        $set_id_list[] = Certifications::CERT_A;
        $set_id_list[] = Certifications::CERT_G;
        $set_id_list[] = Certifications::CERT_VOICE_P;

        $cert_ary = $prod->certificationsArray();
        $this->assertNotEquals(count($cert_ary), count($set_id_list), "counts of certifications should not match the count of the input list");

        $rv = $prod->setCertifications($set_id_list);
        $error_msg = 'Unable to find publishable application; cannot add certification to it.';
        $errors = $prod->errors;
        $this->assertEquals($errors['cert_id'][0], $error_msg, "error message does not match");
        $this->assertFalse($rv, "should not have been able to setCertifications()");

    }

    
}
?>
