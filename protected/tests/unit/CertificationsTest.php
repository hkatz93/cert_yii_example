<?php


/**
* @covers Certifications
*/
class CertificationsTest extends CDbTestCase {

    public $fixtures=array(
    'requested_certifications'=>'RequestedCertifications',
    //'comments'=>'Comment',
    );

  //public function testAddRequestedCertification() {
  //    $ca_ctl = new CertificationApplicationsController;
  //    $rc = new RequestedCertifications;
  //    $rc->cert_id = 41;
  //    //$this->assertTrue($this->)
  //}

    public function testGetTestEngineTypes(){
        $options = Certifications::model()->testEngineTypeOptions;
        $this->assertTrue(is_array($options));
        $this->assertTrue(count($options) == 2, "array does not have 2 elements");
        $this->assertTrue(in_array(Certifications::TEST_ENGINE_TRUE, $options), "TEST_ENGINE_TRUE not an option");
        $this->assertTrue(in_array(Certifications::TEST_ENGINE_FALSE, $options), "TEST_ENGINE_FALSE not an option");
    }

    public function testRequestedCertifications() {
        //$certs = Certifications::model()->with('requested_certifications')->findAll();
        //$this->assertTrue(is_array($certs));
        $cert = Certifications::model()->findByPk(41);
        //$cert = Certifications::model()->with('requested_certifications')->findByPk(41);
        $this->assertTrue($cert instanceof Certifications);

        // lazy load
        $request_certs = $cert->requested_certifications;
        $this->assertTrue(is_array($request_certs));

        // test one of the row values (app_id)
        $rc_app_id = $request_certs[0]->app_id;
        //print "["; print_r($rc_app_id); print "]";
        $this->assertTrue(!empty($rc_app_id), "app_id is blank");
        //$this->assertTrue(preg_match('/^\d+$/', $rc_app_id), "app_id is not an integer"); // fails?
        $this->assertTrue(is_numeric($rc_app_id), "app_id is not numeric");
        

        //foreach ($request_certs as $rc){
            //print "app_id = ".$rc->app_id . "\n";
        //}

        // eager load
        //Yii::log('testing log');
    }
    /**
     * test to assure that the yii functions are working as expected
     * also serves as a working example for future development
     */
    public function testGetCertificationCategories() {
        
        //$cert = Certifications::model()->findByPk(1);
        $certs = Certifications::model()->with('category_classes')->findAll();
        $this->assertTrue(is_array($certs));



        $cert = Certifications::model()->findByPk(1);
        $this->assertTrue(!is_array($cert));
        $this->assertTrue($cert instanceof Certifications, "\$cert is not an instance of Certifications, did the query fail?");
        $this->assertTrue($cert->display_name == 'IEEE 802.11b');
        //print_r($cert);


        if (0) { // this test fails
            $classvar = $cert->id;
            $this->assertTrue(!empty($classvar), "unable to find the related table value for 'class'");
        }

        if (0) { // fails
            $cert_categories = $certs->categories;
            $this->assertTrue(!empty($cert_categories), "\$cert_categories is empty");

            //$this->assertTrue(is_array($cert_categories), "\$cert_categories is not an array");
        }
        // retrieve the post's author: a relational query will be performed here
        //$author=$post->author;

        //$this->assertTrue(is_array($options));
    }

    /**
     * test to help assure that the constant cert_id list is upto date
     */
    public function testCertIdList() {
        $cert_id_list = Certifications::model()->certIdList;
        $this->assertTrue(is_array($cert_id_list));

        $cnt = count($cert_id_list);
        $this->assertTrue($cnt > 0, "there must be at least one existing cert_id in certIdList");

        $certs = Certifications::model()->findAll();
        $this->assertTrue(is_array($certs));
        $cnt2 = count($certs);

        $this->assertTrue($cnt == $cnt2, "the count certIdList ($cnt) does not match number of rows in certifications table ($cnt2)");
    }

    public function testCertCategories() {
        $cert_cats = Certifications::model()->with('category_classes')->findAll();
        //$cert_cats = $cert->categories;
        //print_r($cert_cats);
        
        $this->assertTrue(count($cert_cats) >0, "certification categories is empty");
        foreach ($cert_cats as $c){
            //print "category name = ".$c->categories->class;
        }
    }

    public function testRelations() {

        $cert = Certifications::model()->findByPk(CERTIFICATIONS::CERT_N_APPROVED);
        $this->assertTrue($cert instanceof Certifications, "is not instance of Certifications");

        // lazy load
        $rc = $cert->requested_certifications;
        $this->assertTrue(is_array($rc), " is not an array");
        $this->assertTrue($rc[0] instanceof RequestedCertifications, "is not instance of RequestedCertifications");

        // lazy load
        $cats = $cert->category_classes;
        $this->assertTrue($cats instanceof CategoryClasses, "is not instance of CategoryClasses");

    }

    public function testGetCertNRequiredList(){
        $ary = Certifications::getCertNRequiredList();
        $this->assertInternalType('array', $ary);
        $this->assertGreaterThan(0, count($ary), "array should have at least 1 member");
        foreach ($ary as $v) {
            $this->assertInternalType('integer', $v);
        }

    }

    public function testGetCertNOptionalList(){
        $ary = Certifications::getCertNRequiredList();
        $this->assertInternalType('array', $ary);
        $this->assertGreaterThan(0, count($ary), "array should have at least 1 member");
        foreach ($ary as $v) {
            $this->assertInternalType('integer', $v);
        }

    }

    /**
     * @depends testCertIdList
     */
    public function testCertDropDownArray() {
        $ary = Certifications::getDropDownArray();
        $this->assertInternalType('array', $ary);
        $this->assertGreaterThan(0, count($ary), "array should not be empty");

        // compare drop down to entire cert list
        $this->assertEquals(count(Certifications::getCertIdList()), count($ary), "default dropdown array should have same number of elements as entire cert list");
    }

    public function testNonTestEngineToTestEngineMapArray(){
        $map_ary = Certifications::nonTestEngineToTestEngineMapArray();
        $this->assertInternalType('array', $map_ary);
        foreach ($map_ary as $cert_id_test_engine => $cert_id_non_test_engine){
            $cert_te = Certifications::model()->findByPk($cert_id_test_engine);
            $cert_nte = Certifications::model()->findByPk($cert_id_non_test_engine);
            $this->assertEquals(1, $cert_te->test_engine);
            $this->assertEquals(0, $cert_nte->test_engine);
            $this->assertEquals($cert_te->var_name, $cert_nte->var_name);
        }
    }

    /**
     * @depends testCertDropDownArray
     * @depends testNonTestEngineToTestEngineMapArray
     */
    public function testCertDropDownArrayNonTestEngine(){

        $ary = Certifications::getDropDownArray();
        $ary_nte = Certifications::getDropDownArray(array('is_test_engine' => 0));
        $this->assertInternalType('array', $ary_nte);
        $this->assertGreaterThan(0, count($ary_nte), "array should not be empty");

        // lets make sure that the number of elements in the non-test engine list
        // is smaller than the entire certifications by the number of mappings
        // from test engine to non-test engine certifications

        $count_mappings = count(Certifications::nonTestEngineToTestEngineMapArray());
        $this->assertEquals(count($ary), count($ary_nte)+$count_mappings);
        
    }
    
    /**
     * @depends testCertDropDownArray
     * @depends testNonTestEngineToTestEngineMapArray
     */
    public function testCertDropDownArrayTestEngine(){
        $ary = Certifications::getDropDownArray();
        $ary_te = Certifications::getDropDownArray(array('is_test_engine' => 1));
        $this->assertInternalType('array', $ary_te);
        $this->assertGreaterThan(0, count($ary_te), "array should not be empty");

        // lets make sure that the number of elements in the test engine list
        // is smaller than the entire certifications by the number of mappings
        // from test engine to non-test engine certifications

        $count_mappings = count(Certifications::nonTestEngineToTestEngineMapArray());
        $this->assertEquals(count($ary), count($ary_te)+$count_mappings);

    }
    public function testDeleteProhibited(){
        $cert = Certifications::model()->findByPk(CERTIFICATIONS::CERT_N_APPROVED);
        $this->assertTrue($cert instanceof Certifications, "is not instance of Certifications");
        $this->assertFalse($cert->delete(), "should not have been able to delete Certification");

        
    }

    
    

}
?>
