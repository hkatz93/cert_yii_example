<?php

/**
* @covers RequestedCertifications
*/
class RequestedCertificationsTest extends CDbTestCase {

    public $fixtures=array(
    'products' => 'Products',
    'requested_certifications'=>'RequestedCertifications',
    'certification_applications'=>'CertificationApplications',
    'test_results'=>'TestResults',
    //'test_data'=>'TestData',
    //'test_data'=>'TestData', // model does not exist yet
    //'product_certifications'=>'ProductCertifications',
    );

  /**
   * @desc shows how validation makes sure that cert_id and app_id are legal
   */
  public function testCertIdAndAppIdValidation(){
    // create a new requested certification
    $ca = new RequestedCertifications();
    $tmp = array();
    $tmp['cert_id'] = '999'; // bad number
    $tmp['app_id'] = '1'; // ok number
    $ca->setAttributes($tmp);
    $this->assertFalse($ca->save(), " creation of cert_id {$tmp['cert_id']} should have been prevented");
    $this->assertTrue($ca->save(false), " creation of cert_id {$tmp['cert_id']} should not have been prevented");

    // create a new requested certification
    $ca = new RequestedCertifications();
    $tmp = array();
    $tmp['cert_id'] = Certifications::CERT_D; // ok number
    $tmp['app_id'] = '9999999'; // bad
    $ca->setAttributes($tmp);
    $this->assertFalse($ca->save(), " creation with app_id = {$tmp['app_id']} should have been prevented");
    $this->assertTrue($ca->save(false), " creation with app_id {$tmp['app_id']} should not have been prevented");

    // create a new requested certification
    $ca = new RequestedCertifications();
    $tmp = array();
    $tmp['cert_id'] = Certifications::CERT_A_TEST_ENGINE; // OK number
    $tmp['app_id'] = '1'; // ok
    $ca->setAttributes($tmp);
    $this->assertTrue($ca->save(), " creation with cert_id {$tmp['cert_id']} should not have been prevented");

    // create a new requested certification
    $ca = new RequestedCertifications();
    $tmp = array();
    $tmp['cert_id'] = 9999999; // bad
    $tmp['app_id'] = 9999999; // bad
    $ca->setAttributes($tmp);
    $this->assertFalse($ca->save(), " creation with cert_id {$tmp['cert_id']} and app_id {$tmp['app_id']} should have been prevented");
  }

  /**
   * @depends testCertIdAndAppIdValidation
   */
  public function testPreventDuplicateAppIdCertIdRow() {
      $this->markTestSkipped(
              'This functionality is important, but requires other fixes before it can work'
        );
   
    $ca = new RequestedCertifications();
    $tmp = array();
    $tmp['cert_id'] = Certifications::CERT_A_TEST_ENGINE; // OK number
    $tmp['app_id'] = '1'; // ok
    $ca->setAttributes($tmp);
    $this->assertTrue($ca->save(), " creation with cert_id {$tmp['cert_id']} should not have been prevented");

    // do the exact same insert, which should be prevented
    $ca = new RequestedCertifications();
    $tmp = array();
    $tmp['cert_id'] = Certifications::CERT_A_TEST_ENGINE; // OK number
    $tmp['app_id'] = '1'; // ok
    $ca->setAttributes($tmp);
    $this->assertFalse($ca->save(), " creation of duplicate app_id {$tmp['app_id']} cert_id {$tmp['cert_id']} should have been prevented");

  }
  
  public function testCRUD() {


    // create a new requested certification
    $ca = new RequestedCertifications();
    $tmp = array();
    $tmp['cert_id'] = '1';
    $tmp['app_id'] = '9800';
    $ca->setAttributes($tmp);
    $this->assertTrue($ca->save(false));

    
    //READ back the newly created application
    $retrievedCa=RequestedCertifications::model()->findByPk($ca->request_id);
    $this->assertTrue($retrievedCa instanceof RequestedCertifications);
    $this->assertEquals($tmp['app_id'],$retrievedCa->app_id,
            "\$tmp['app_id'] ({$tmp['app_id']} does not equal,\$retrievedCa->app_id ({$retrievedCa->app_id})");
    if (1){
    //UPDATE the newly created requested certification
    $cert_id = 7;
    $ca->cert_id = $cert_id;
    $this->assertTrue($ca->save(false), "not able to save cert_id=7");

    //read back the record again to ensure the update worked
    $retrievedCa=RequestedCertifications::model()->findByPk($ca->request_id);
    $this->assertTrue($retrievedCa instanceof RequestedCertifications);
    $this->assertEquals($cert_id,$retrievedCa->cert_id);

    //DELETE the requested cert
    $ca_id = $ca->request_id;
    $this->assertTrue($ca->delete());
    $deletedCa=RequestedCertifications::model()->findByPk($ca_id);
    $this->assertEquals(NULL,$deletedCa);
    }
  }

  public function testRelationsCertifications() {
        $rc = $this->requested_certifications('1'); // from fixture, which is same as db since db is reset on unit test load
       // Test for certification_applications relation
        $apps = $rc->certification_applications;
        //print_r($apps);
        $this->assertTrue(is_object($apps), "is not an object");
        $this->assertTrue($apps instanceof CertificationApplications);

        // Test for certifications relation
        // lazy load
        $cert = $rc->certifications;
        //print_r($cert);
        $this->assertTrue(is_object($cert), "is not an object");
        $this->assertTrue($cert instanceof Certifications, "is not instance of Certifications");


  }

  public function testRelationsTestResults() {
        //$rc = RequestedCertifications::model()->findByPk(1); // from database
        $rc = $this->requested_certifications('1'); // from fixture, which is same as db since db is reset on unit test load
        
        // Test for test_results relation
        // lazy load
        $tr = $rc->test_results;
        //print_r($cert);
        $this->assertTrue(is_object($tr), "is not an object");
        $this->assertTrue($tr instanceof TestResults, "is not instance of TestResults");
        
        // test that all have passed for this one
        //print "request_id='".$tr->request_id."', result='".$tr->result."'\n";
        $this->assertTrue($tr->result == TestResults::PASS, "result for request_id=".$tr->request_id. " should have passed");


  }
  
  
  
  /**
   * WARNING: somehow when there is a test after this testDelete() it causes a data error in ProductCertificationTest
   * The error is request_id=1 should not exist
   */
  public function testDelete(){
        $rc = $this->requested_certifications('0'); // from fixture, which is same as db since db is reset on unit test load

        // gather related information
        $cert_pk = $rc->certifications->cert_id;
        //var_dump($cert_pk);
        $this->assertTrue(is_numeric($cert_pk), "\$cert_pk = $cert_pk, is not a numeric");

        $test_result_pk = $rc->test_results->result_id;
        $this->assertTrue(is_numeric($test_result_pk ), "is not numeric");

        $app_pk = $rc->certification_applications->app_id;
        $this->assertTrue(is_numeric($app_pk), "is not numeric");

        $rc->delete();

        // this should not be deleted
        $app = CertificationApplications::model()->findByPk($app_pk);
        $this->assertTrue(is_object($app));
        $this->assertTrue($app instanceof CertificationApplications, "application should not have been deleted");

        // this should not be deleted
        $cert = Certifications::model()->findByPk($cert_pk);
        $this->assertTrue(is_object($cert));
        $this->assertTrue($cert instanceof Certifications, "cert should not have been deleted");

        // this should have been deleted
        $test_result = TestResults::model()->findByPk($test_result_pk);
        $this->assertFalse($test_result instanceof TestResults, "test result should have been deleted");
  }
       
  
  /**
   * tests whether a given certification should have external data or not
   * it can be Yes|No|Maybe
   */
  public function testShouldHaveExternalDataFile() {
        // Stop here and mark this test as incomplete.
        //$this->markTestIncomplete(
        //  'This test has not been implemented yet.'
       // );

        $ary_yes = array(
                Certifications::CERT_WFD,
                Certifications::CERT_WFD_TEST_ENGINE,
                Certifications::CERT_WPS2_PIN,
                Certifications::CERT_CWG_RF // note: should not be copied to dependents
             );

        foreach ($ary_yes as $cert_id){
            $result = RequestedCertifications::shouldHaveExternalDataFile($cert_id);
            $this->assertEquals($result, RequestedCertifications::EXT_DATA_YES, "certifications should have external data file (cert_id=$cert_id)");
        }
        $ary_maybe = array(
                Certifications::CERT_A,
                Certifications::CERT_A_TEST_ENGINE,
                Certifications::CERT_B,
                Certifications::CERT_B_TEST_ENGINE,
                Certifications::CERT_G,
                Certifications::CERT_G_TEST_ENGINE,
                Certifications::CERT_N,
                Certifications::CERT_N_APPROVED,
                Certifications::CERT_N_APPROVED_TEST_ENGINE
             );
        
        foreach ($ary_maybe as $cert_id){
            $result = RequestedCertifications::shouldHaveExternalDataFile($cert_id);
            //$this->assertEquals($result, RequestedCertifications::EXT_DATA_MAYBE, "certifications should have external data file (cert_id=$cert_id)");
            $this->assertEquals($result, RequestedCertifications::EXT_DATA_NO, "certifications should not have external data file (cert_id=$cert_id)");
        }

        // check all of the other certifications?
        $all_cert_ids = Certifications::getCertIdList();
        
        // remove the lists we have already checked
        $ary_no = array_diff($all_cert_ids, $ary_yes, $ary_maybe);
        foreach ($ary_no as $cert_id){
            $result = RequestedCertifications::shouldHaveExternalDataFile($cert_id);
            $this->assertEquals($result, RequestedCertifications::EXT_DATA_NO, "certifications should not have external data file (cert_id=$cert_id)");
        }

  }

 
  public function testExternalDataFilename() {
      // fake Wi-Fi direct certification
      $prod = $this->products('0');
      $prod->addCertification(Certifications::CERT_WFD);
      
      // find the rc with WFD
      $rc = RequestedCertifications::model()->find('cert_id=:cert_id', array('cert_id'=>Certifications::CERT_WFD));
      $this->assertTrue($rc instanceof RequestedCertifications, "not able to load requested certification with WFD");
      //$rc->uploadExternalData($dummy_data, $extension); // fake this method for now
      $filename = Yii::app()->params->uploaded_data_dir .'/'. 'wfd_'.$rc->request_id.'.xls';
      $dummy_data = 'test stuff';
      file_put_contents($filename, $dummy_data);
      //print "external file path ='".($rc->getExternalFilePath())."'";
      $this->assertTrue(file_exists($rc->getExternalFilePath()), "not able to find external file");
  }
    

}
?>
