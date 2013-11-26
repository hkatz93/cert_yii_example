<?php

/**
* @covers RequestedCertifications
 *
 * This is created as a seperate test suite, so that we can minimize the number of times
 * the large amount of data in test_data is reloaded using the fixture
*/
class RequestedCertificationsTestWithLabData extends CDbTestCase {

    public $fixtures=array(
    'products' => 'Products',
    'requested_certifications'=>'RequestedCertifications',
    'certification_applications'=>'CertificationApplications',
    'test_results'=>'TestResults',
    'test_data'=>'TestData',
    //'product_certifications'=>'ProductCertifications',
    );

  
  public function testRelationsTestData() {
      // Test for test_data relation
        // lets find a requested certifications that has some data (802.11n)
        $rc = RequestedCertifications::model()->find('cert_id=:cert_id', array(':cert_id'=>Certifications::CERT_N_APPROVED)); // from database
        //print "rc = ";print_r($rc);
        $this->assertTrue($rc instanceof RequestedCertifications, "is not an instance of RequestedCertifications");
        $td_list = $rc->test_data;
        //$td_list = TestData::model()->find('request_id=:request_id', array(':request_id'=>$rc->request_id)); // from database
        //print "td_list = ";print_r($td_list);
        $this->assertTrue(is_array($td_list), "is not an array");
        //print_r($td_list[0]);
        $this->assertTrue($td_list[0] instanceof TestData, "is not instance of TestData");

  }
  public function testDeleteCertABGWorksWithNoLabData(){
        $rc = RequestedCertifications::model()->find('cert_id=:cert_id',
                array(':cert_id'=>Certifications::CERT_A));
        $this->assertTrue($rc instanceof RequestedCertifications);
        $td = $rc->test_data;
        $this->assertTrue(empty($td), "should have had no test data");
        $this->assertTrue($rc->delete(), "should not have prevented delete");

        $rc = RequestedCertifications::model()->find('cert_id=:cert_id',
                array(':cert_id'=>Certifications::CERT_B));
        $this->assertTrue($rc instanceof RequestedCertifications);
        $td = $rc->test_data;
        $this->assertTrue(empty($td), "should have had no test data");
        $this->assertTrue($rc->delete(), "should not have prevented delete");

        $rc = RequestedCertifications::model()->find('cert_id=:cert_id',
                array(':cert_id'=>Certifications::CERT_G));
        $this->assertTrue($rc instanceof RequestedCertifications);
        $td = $rc->test_data;
        $this->assertTrue(empty($td), "should have had no test data");
        $this->assertTrue($rc->delete(), "should not have prevented delete");
  }
  
  public function testDeleteCertN(){

      // test to see that the cert_n results are moved properly
        $req_cert_n = RequestedCertifications::model()->find('cert_id=:cert_id',
                array(':cert_id'=>Certifications::CERT_N_APPROVED));

        $this->assertTrue($req_cert_n instanceof RequestedCertifications, "should have found one with 802.11n");
        $req_cert_n_id = $req_cert_n->request_id;
        $this->assertTrue(is_numeric($req_cert_n_id), "primary key for requested_certifications should have been numeric");

        if (0){

            $results = TestResults::model()->findAll('request_id=:request_id', array(':request_id'=>$req_cert_n_id));
            $this->assertTrue(is_array($results), "\$results should have been an array");
            $this->assertTrue($results[0] instanceof TestResults, "should have found one result for 802.11n");
        }
        $app_id = $req_cert_n->app_id;

        // lets look for specific 802.11n lab results that will be moving from n to b
        $test_data = TestData::model()->with('test_fields')->find("request_id=:request_id and field_name='Is 802.11n Device'", array(":request_id"=>$req_cert_n_id));
        $this->assertTrue(!is_array($test_data) , " is an array");
        $this->assertTrue($test_data instanceof  TestData, " is not an instance of TestData");
        $this->assertTrue($test_data->data == 'Yes', "Is 802.11n Device <> 'Yes'");

        // check to see if 802.11b exists with in the app before we delete
        //
        $req_cert_b = RequestedCertifications::model()->find('app_id=:app_id and (cert_id=:cert_id1 or cert_id=:cert_id2)',
                    array(  ':app_id'=>$app_id,
                            ':cert_id1'=>  Certifications::CERT_B,
                            ':cert_id2'=>  Certifications::CERT_B_TEST_ENGINE,
                        )
                    );
        $this->assertTrue($req_cert_b instanceof RequestedCertifications, "should have found one with 802.11b");


        $rv = $req_cert_n->delete();
        $this->assertTrue($rv, "should have allowed 802.11n to be deleted");

        // find test results for 802.11b (which is where they should have gone)


        //$req_cert = RequestedCertifications::model()->find('cert_id=:cert_id', array(':cert_id'=>Certifications::CERT_B));
        //
        $req_cert_n2 = RequestedCertifications::model()->find('app_id=:app_id and (cert_id=:cert_id1 or cert_id=:cert_id2)',
                    array(  ':app_id'=>$app_id,
                            ':cert_id1'=>  Certifications::CERT_N_APPROVED,
                            ':cert_id2'=>  Certifications::CERT_N_APPROVED_TEST_ENGINE,
                        )
                    );
        $this->assertFalse($req_cert_n2 instanceof RequestedCertifications, "should not have found one with 802.11n");



        // check to see if 802.11b exists with in the app
        //
        $req_cert_b = RequestedCertifications::model()->find('app_id=:app_id and (cert_id=:cert_id1 or cert_id=:cert_id2)',
                    array(  ':app_id'=>$app_id,
                            ':cert_id1'=>  Certifications::CERT_B,
                            ':cert_id2'=>  Certifications::CERT_B_TEST_ENGINE,
                        )
                    );
        $this->assertTrue($req_cert_b instanceof RequestedCertifications, "should have found one with 802.11b");
        //$test_results =$req_cert->test_results;
        //$this->assertTrue(is_array($test_results), "\$test_results should have been an array");
        //$this->assertTrue($req_cert instanceof RequestedCertifications, "should have found one with 802.11b");

         // lets look for specific 802.11n lab results that will be moving from n to b
        $test_data = TestData::model()->with('test_fields')->find("request_id=:request_id and field_name='Is 802.11n Device'", array(":request_id"=>$req_cert_n_id));
        $this->assertTrue(!is_array($test_data) , " is an array");
        $this->assertTrue($test_data instanceof  TestData, " is not an instance of TestData");
        $this->assertTrue($test_data->data == 'No', "Is 802.11n Device <> 'No'");

        // check to see if the test results are now associated with cert_b
        $rc = RequestedCertifications::model()->findByPk($req_cert_n_id);
        $this->assertEquals(Certifications::CERT_B, $rc->cert_id, "should have changed the cert_id to cert_b");
        $this->assertEquals($req_cert_n->cert_id, $rc->data_fields_cert_id, "data_fields_cert_id to cert_n");
        $td = $rc->test_data ;
        $this->assertTrue($td[0] instanceof TestData, "cert_b should have related TestData after cert_n delete");
  }



    /**
     * prevent deleting 802.11abg, when the requested certification has lab data
     * @depends testDeleteCertN
     */
    public function testDeleteBPreventionWhenHasLabData(){
        $req_cert_n = RequestedCertifications::model()->find('cert_id=:cert_id',
                array(':cert_id'=>Certifications::CERT_N_APPROVED));
        $req_cert_n->delete();

        $req_cert_b = RequestedCertifications::model()->find('cert_id=:cert_id',
                array(':cert_id'=>Certifications::CERT_B));
        $this->assertFalse($req_cert_b->delete(), "should have prevented cert_b delete when has test data");
        // Stop here and mark this test as incomplete.
        //$this->markTestIncomplete(
          //'This test has not been implemented yet. It should not apply to Cert N which has special code to permit this'
        //);
    }

    public function testCopyLabResults() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    public function testCopyExternalData() {
        
        // lets fake some data
        // add Wi-Fi direct to an application and add a dummy external data
        $ca = $this->certification_applications('0');
        $this->assertTrue($ca->addCertification(Certifications::CERT_WFD), "not able to add CERT_WFD to app");
        
        $rc_source = RequestedCertifications::model()->find('cert_id=:cert_id', array('cert_id'=>Certifications::CERT_WFD));
        
        // create initial fake data for source requested certification
        $write_path = Yii::app()->params->uploaded_data_dir;
        //print "write path = $write_path\n";
        $dummy_data = 'WFD dummy data';
        $filename = $write_path . $rc_source->request_id;
        file_put_contents($filename, $dummy_data);
        $this->assertTrue(file_exists($filename), "file at $filename does not exist");
        
        
        // create a new requested certification and copy the data to it
        $prod = $this->products('1');
        $ca_list = $prod->certification_applications;
        $ca = (!is_array($ca_list)) ? $ca_list : $ca_list[0];
        $this->assertTrue($ca instanceof CertificationApplications);
        $rc_target = new RequestedCertifications();
        $rc_target->app_id = $ca->app_id;
        $rc_target->cert_id = $rc_source->cert_id;
        $this->assertTrue($rc_target->save(), "unable to save rc_target");
        
        $rc_target->copyExternalData($rc_source);
        
        $file_path = $rc_target->getExternalFilePath();
        
        $this->assertTrue(file_exists($file_path), "file at $file_path does not exist, it did not copy over");
        
        
        // attempt to copy over existing data, it should fail
        
        // attempt to force copy over existing data, it should succeed
        
    }
  
    /**
     * test for existance of external data
     */
    /*
    public function testHasExternalData() {
         // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
        
       // test to see if a given cert should have associated external data
       // if it does, retrieve the file path
       // if not, return null

    }
     * */
     
}
?>
