<?php
/*
 * unit test for model CerificationApplications
 * and open the template in the editor.
 */

class CertificationApplicationsTest extends CDbTestCase {

    public $fixtures=array(
    'certification_applications'=>'CertificationApplications',
    'product_certifications'=>'ProductCertifications',
    'products'=>'Products',
    'requested_certifications'=>'RequestedCertifications',
    //'test_data'=>'TestData',
    'test_results'=>'TestResults',
    'activity_log'=>'ActivityLog', // just blanked out
    );

  //public function testAddRequestedCertification() {
  //    $ca_ctl = new CertificationApplicationsController;
  //    $rc = new RequestedCertifications;
  //    $rc->cert_id = 41;
  //    //$this->assertTrue($this->)
  //}

    public function testDatestr2YYYYMMDD() {
        $date_in = '1/6/2002 03:02:32';
        $date_out ='2002-01-06 03:02:32';
        $this->assertEquals($date_out, CertificationApplications::datestr2YYYYMMDD($date_in));

        $this->assertEquals('0000-00-00', CertificationApplications::datestr2YYYYMMDD(0));
        $this->assertEquals('0000-00-00', CertificationApplications::datestr2YYYYMMDD('0000-00-00'));
        $this->assertEquals('0000-00-00 00:00:00', CertificationApplications::datestr2YYYYMMDD('0000-00-00 00:00:00'));
        $this->assertEquals($date_out, CertificationApplications::datestr2YYYYMMDD($date_in));
    }

    public function testTime2MMDDYYYY() {
        //$date_in = '1/6/2002 03:02:32';
        $date_in ='2002-01-06 03:02:32'; //2000-03-29 00:00:00
        $time = strtotime($date_in);

        //$date_out ='2002-01-06 03:02:32';
        $date_out = '1/6/2002 03:02:32';
        $this->assertEquals($date_out, CertificationApplications::time2MMDDYYYY($time));
        $this->assertEquals($date_out, CertificationApplications::time2MMDDYYYY(strtotime($date_out)));

        // special case when zero
        $this->assertEquals('0000-00-00 00:00:00', CertificationApplications::time2MMDDYYYY(0));
        $this->assertEquals('0000-00-00', CertificationApplications::time2MMDDYYYY(0, false));

    }

    /**
     * tests ability of model to convert date formats to MM/DD/YYYY ##:##:## on the fly
     * @depends testDatestr2YYYYMMDD
     * @depends testTime2MMDDYYYY
     */
    public function testDateSaveAndLoadNewFormat() {
        //$this->markTestSkipped(
        //      'This functionality not completed yet'
        //);
        $date1 = '1/1/2001 13:11:23';
        $date2 = '1/2/2002 13:12:24';
        $date3 = '1/3/2003 13:13:25';
        $date4 = '1/4/2004 13:14:26';
        $date5 = '1/5/2005 13:15:27';
        $date6 = '1/6/2006 13:16:28';
        $date6_expected = '1/6/2006'; // this is a date field (not datetime)

        //$date_notime = '1/2/2001';
        $ca = $this->certification_applications('0');
        $app_id = $ca->app_id;

        $ca = CertificationApplications::model()->findByPk($app_id);
        //print __FUNCTION__.": app_id = $app_id\n";
        //print __FUNCTION__.": date before set="; print $ca1->date_lab_accepted; print "\n";
        
        $ca->date_submitted = $date1;
        $ca->date_lab_accepted = $date2;
        $ca->date_finalized_results = $date3;
        $ca->date_staff_reviewed = $date4;
        $ca->date_certified = $date5;
        $ca->deferred_date = $date6;
        
        //$ca->deferred_date = $date;
        //print __FUNCTION__.": date after set="; print $ca1->date_lab_accepted; print "\n";

        $ca->save();
        //print __FUNCTION__.": ca1 date after save="; print $ca1->date_lab_accepted; print "\n";

        $ca2 = CertificationApplications::model()->findByPk($app_id);
        //print __FUNCTION__.": date after save="; print $ca2->date_lab_accepted; print "\n";

        
        $this->assertEquals($date1, $ca2->date_submitted);
        $this->assertEquals($date2, $ca2->date_lab_accepted);
        $this->assertEquals($date3, $ca2->date_finalized_results);
        $this->assertEquals($date4, $ca2->date_staff_reviewed);
        $this->assertEquals($date5, $ca2->date_certified);
        $this->assertEquals($date6_expected, $ca2->deferred_date);


        // test special cases where date is "zero"

    }
    
    /**
     * special cases are needed to test dates when value is zero
     * @depends testDatestr2YYYYMMDD
     * @depends testTime2MMDDYYYY
     */
    public function testDateSaveAndLoadNewFormatWithZeroValue(){
        $date_in = '0000-00-00';
        $ca = $this->certification_applications('0');
        $ca->deferred_date = $date_in;
        $ca->save();
        $ca2 = CertificationApplications::model()->findByPk($ca->app_id);
        $this->assertEquals('0000-00-00', $ca2->deferred_date);
    }
	
   public function testSaveStreams() {
	$ca = $this->certification_applications('0');
        $ca->certified_tx_spatial_stream_24 = Products::MAX_CERTIFIED_STREAMS;
        $ca->certified_rx_spatial_stream_24 = Products::MAX_CERTIFIED_STREAMS;
        $ca->certified_tx_spatial_stream_50 = Products::MAX_CERTIFIED_STREAMS;
        $ca->certified_rx_spatial_stream_50 = Products::MAX_CERTIFIED_STREAMS;
        $ca->certified_tx_spatial_stream_50_ac = Products::MAX_CERTIFIED_STREAMS;
        $ca->certified_rx_spatial_stream_50_ac = Products::MAX_CERTIFIED_STREAMS;
	$rv = $ca->save();
        $this->assertTrue($rv, "unable to save certified spatial streams");
        
	// test a failure
	$ca2 = CertificationApplications::model()->findByPk($ca->app_id);
        $ca2->certified_tx_spatial_stream_50_ac = Products::MAX_CERTIFIED_STREAMS + 1;
        $rv = $ca2->save();
        $this->assertFalse($rv, "should have prevents saving invalid number of streams");
   }
    
    public function testRelationsCertifications() {
        $cert_app = CertificationApplications::model()->findByPk(1); // from database
        $cert_app = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $this->assertTrue($cert_app instanceof CertificationApplications);
        // lazy load
        $certs = $cert_app->certifications;
        $this->assertTrue(is_array($certs), "is not an array");
        $this->assertTrue($certs[0] instanceof Certifications, "is not instance of Certifications");
    }

    public function testRelationsLabs() {
        $cert_app = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $this->assertTrue($cert_app instanceof CertificationApplications);
        // lazy load
        $lab = $cert_app->labs;
        //$this->assertTrue(is_array($req_certs), "is not an array");
        $this->assertTrue($lab instanceof Labs, "is not instance of Labs");
    }

    public function testRelationsRequestedCertifications(){
        $cert_app = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $this->assertTrue($cert_app instanceof CertificationApplications);
        // lazy load
        $req_certs = $cert_app->requested_certifications;
        $this->assertTrue(is_array($req_certs), "is not an array");
        $this->assertTrue($req_certs[0] instanceof RequestedCertifications, "is not instance of RequestedCertifications");

    }
    
    public function testRelationsUsers(){
        $cert_app = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $this->assertTrue($cert_app instanceof CertificationApplications);
        $user = $cert_app->users;
        //$this->assertTrue(is_array($req_certs), "is not an array");
        $this->assertTrue($user instanceof Users, "is not instance of Users");
    }

    public function testRelationsProducts(){
        $cert_app = $this->certification_applications('0'); // from fixture, which is database since it resets it on load

        $prod = $cert_app->products;
        //$this->assertTrue(is_array($req_certs), "is not an array");
        $this->assertTrue($prod instanceof Products, "is not instance of Products");
    }

public function testValidationPendingOsOtherError(){
        $cert_app = $this->certification_applications('0');
        $cert_app->pending_os_id = Os::OS_ID_OTHER;
		$cert_app->pending_os_other = '';
        $this->assertFalse($cert_app->save(), "should have prevented saving a blank os other when 'Other' is selected");
}
public function testValidationInitialOsOtherError(){
		$cert_app = $this->certification_applications('0');
        $cert_app->initial_os_id = Os::OS_ID_OTHER;
		$cert_app->initial_os_other = '';
        $this->assertFalse($cert_app->save(), "should have prevented saving a blank os other when 'Other' is selected");
}
public function testValidationPendingOsOtherOk(){		
	   $cert_app = $this->certification_applications('0');
        $cert_app->pending_os_id = 1; // Android
	   $cert_app->pending_os_other = '';
        $this->assertTrue($cert_app->save(), "should not have prevented saving a os_id");
}
public function testValidationInitialOsOtherOk(){
		$cert_app = $this->certification_applications('0');
        $cert_app->initial_os_id = 1; // Android
	   $cert_app->initial_os_other = '';
        $this->assertTrue($cert_app->save(), "should not have prevented saving a os_id");
    }
		
//	public function testRelationsOs(){
//        $cert_app = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
//
//        $cert_app = $cert_app->pending_os;
//        //$this->assertTrue(is_array($req_certs), "is not an array");
//        $this->assertTrue($cert_app instanceof Os, "is not instance of Os");
//
//		$cert_app = $cert_app->initial_os;
//        //$this->assertTrue(is_array($req_certs), "is not an array");
//        $this->assertTrue($cert_app instanceof Os, "is not instance of Os");				
//    }
//		
    public function testScopeCompleted(){
        $prod_id = $this->products('0')->product_id;

        // now lets grab data based on named scope
        $ca = CertificationApplications::model()->completed()->findAll("product_id=:product_id", array('product_id'=>$prod_id));
        $this->assertTrue(is_array($ca), "not an array");
        $this->assertTrue(count($ca)==1, " should only be one match");
    }

    public function testScopePublishable(){
        $prod_id = $this->products('0')->product_id;

        // now lets grab data based on named scope "publishable"
        $ca = CertificationApplications::model()->publishable()->findAll("product_id=:product_id", array('product_id'=>$prod_id));
        $this->assertTrue(is_array($ca), "not an array");
        $this->assertTrue(count($ca)==1, " should only be one match");
    }

    /**
     * @depends testScopePublishable
     */
    public function testIsPublishable(){
        $prod_id = $this->products('0')->product_id;

        // now lets grab data based on named scope "publishable"
        $ca = CertificationApplications::model()->publishable()->findAll("product_id=:product_id", array('product_id'=>$prod_id));
        $this->assertTrue($ca[0]->isPublishable(), "app should have been isPublishable()");
        
        // now lets grab a non-publishable app just to test the negative
        $ca = CertificationApplications::model()
                ->find('status != :status', 
                        array('status'=>CertificationApplications::STATUS_COMPLETE));
        $this->assertTrue($ca instanceof CertificationApplications, "not an instance of CertificationApplications");
        $this->assertFalse($ca->isPublishable(), "app should not have been declared publishable");
    }
    
    public function testScopeMostRecent(){
        // get a product
        $prod_id = $this->products('0')->product_id;

        // now lets grab data based on named scope "most_recent"
        $ca = CertificationApplications::model()->most_recent()->findAll("product_id=:product_id", array('product_id'=>$prod_id));
        $this->assertTrue(is_array($ca), "not an array");
        $this->assertTrue(count($ca)>1, " should be > one match");
        // lets make the comparison of submitted date

        $date1 = strtotime("{$ca[0]->date_submitted}");
        $date2 = strtotime("{$ca[1]->date_submitted}");
        //print "date1 = $date1, date2=$date2\n";
        $this->assertTrue($date1 > $date2, " the first row should have been submitted after 2nd row");

        // now lets grab data based on named scope "oldest"
        $ca = CertificationApplications::model()->oldest()->findAll("product_id=:product_id", array('product_id'=>$prod_id));
        $this->assertTrue(is_array($ca), "not an array");
        $this->assertTrue(count($ca)>1, " should be > one match");
        // lets make the comparison of submitted date

        $date1 = strtotime("{$ca[0]->date_submitted}");
        $date2 = strtotime("{$ca[1]->date_submitted}");
        //print "date1 = $date1, date2=$date2\n";
        $this->assertTrue($date1 < $date2, " the first row should have been submitted before 2nd row");
    }

    /**
     * test a combination of a few different scopes and a relation
     * @depends testScopeMostRecent
     * @depends testScopePublishable
     * @depdens testRelationsRequestedCertifications
     */
    /*
    public function testScopeAndRelationMostRecentPublishableRequestedCertifications() {
         $prod = $this->products('0');
         $this->assertTrue($prod instanceof Products, "prod is not instance of Products");
         $cert_list = $prod->certificationsArray();
         $cert_id = $cert_list[0]['cert_id'];
         $ca = CertificationApplications::model()
                    ->publishable()
                    ->most_recent()
                    ->with('requested_certifications')
                    ->find('product_id=:product_id AND cert_id=:cert_id', 
                            array('product_id'=>$prod->product_id, 'cert_id' => $cert_id));
         
         $this->assertTrue($ca instanceof CertificationApplications, "ca is not instance of CertificationsApplications");
         $rc_list = $ca->requested_certifications;
         $this->assertTrue(is_array($rc_list), "rc list is not an array");
         $this->assertTrue($rc_list[0] instanceof RequestedCertifications, "not instance of RequestedCertifications");
    }
     * */
     
    /**
     * @depends testRelationsRequestedCertifications
     */
    public function testCertificationsArray() {
        $ca = $this->certification_applications('0');
        $rows = $ca->certificationsArray();
        $this->assertInternalType('array', $rows);
        $this->assertGreaterThan(0, count($rows));
        $required_flds = array('category', 'cert_id', 'display_name', 'request_id');
        foreach ($required_flds as $fld){
            $this->assertTrue(isset($rows[0][$fld]), "could not find field $fld in the row");
        }

        // test to make sure that this array pulls same as active records
        $this->assertEquals(count($rows), count($ca->requested_certifications), "count does not match requested_certifications relation");
    }

    /**
     * @depends testCertificationsArray
     * @depends testRelationsRequestedCertifications
     */
    public function testCertIdList() {
        $ca = $this->certification_applications('0');
        $cert_id_list = $ca->certIdList();
        $this->assertInternalType('array', $cert_id_list);
        $this->assertGreaterThan(0, count($cert_id_list));
        
        // test to make sure that this array pulls same as active records
        $this->assertEquals(count($cert_id_list), count($ca->requested_certifications), "count does not match requested_certifications relation");
    }
    
    /**
     * @depends testRelationsRequestedCertifications
     */
    public function testAddCertificationToCompletedApplicationWithPassResult(){
        $ca = $this->certification_applications('0'); // complete application
        $this->assertTrue($ca->addCertification(Certifications::CERT_N_40_IN_50), "unable to add certification to application");

        // retrieve the test result of this newly added PASSED cert
        $rc = RequestedCertifications::model()
            ->with('test_results')
            ->find('app_id=:app_id and cert_id=:cert_id', array('app_id'=>$ca->app_id, 'cert_id'=>Certifications::CERT_N_40_IN_50));

        $this->assertTrue($rc instanceof RequestedCertifications, "not able to find recently added requested certification");
        $this->assertEquals(TestResults::PASS, $rc->test_results->result);
    }
    
    /**
     * @depends testRelationsRequestedCertifications
     */
    public function testAddCertificationToCompletedApplicationWithFailResult(){
        $ca = $this->certification_applications('0'); // complete application
        $this->assertTrue($ca->addCertification(Certifications::CERT_N_GREEN_PRE, TestResults::FAIL), "unable to add certification to application");

        // retrieve the test result of this newly added PASSED cert
        $rc = RequestedCertifications::model()
            ->with('test_results')
            ->find('app_id=:app_id and cert_id=:cert_id', array('app_id'=>$ca->app_id, 'cert_id'=>Certifications::CERT_N_GREEN_PRE));

        $this->assertTrue($rc instanceof RequestedCertifications, "not able to find recently added requested certification");
        $this->assertEquals(TestResults::FAIL, $rc->test_results->result);
    }

    /**
     * @depends testRelationsRequestedCertifications
     */
    public function testAddCertificationToCompletedApplicationWithNotTestedResult(){
        $ca = $this->certification_applications('0'); // complete application
        $this->assertTrue($ca->addCertification(Certifications::CERT_N_SGI20, TestResults::NOT_TESTED), "unable to add certification to application");

        // retrieve the test result of this newly added PASSED cert
        $rc = RequestedCertifications::model()
            ->with('test_results')
            ->find('app_id=:app_id and cert_id=:cert_id', array('app_id'=>$ca->app_id, 'cert_id'=>Certifications::CERT_N_SGI20));

        $this->assertTrue($rc instanceof RequestedCertifications, "not able to find recently added requested certification");
        $this->assertEquals((int)TestResults::NOT_TESTED, (int)$rc->test_results->result);
    }

    /**
     * @depends testScopePublishable
     */
    public function testAddCertificationToIncompleteApplication(){
        $ca = $this->certification_applications('1'); // incomplete application

        // assure this is incomplete
        $ca2 = CertificationApplications::model()->publishable()->findByPk($ca->app_id);
        $this->assertFalse($ca2 instanceof CertificationApplications, "should not have found incomplete app in publishable scope");

        $this->assertTrue($ca->addCertification(Certifications::CERT_VOICE_P), "should not have prevent adding certifications to app");
        // Stop here and mark this test as incomplete.
        //$this->markTestIncomplete(
        //  'This test has not been implemented yet.'
        //);
    }

    /**
     * assure that if the application is already public, it will affect the publicly visible certifications
     */
    public function testAddCertificationToPublishedApplicationAffectsProductCertifications(){
        $this->markTestSkipped(
              'updating the product from the application can cause infinite loops if both have afterSave() code that affect one another'
        );
        $ca = $this->certification_applications('0');

        
    }

    public function testAddCertificationBadCertId(){
        $ca = $this->certification_applications('0');
        $this->assertFalse($ca->addCertification(0), "should have prevented adding certification to application");
    }
    public function testAddCertificationDuplicateCertId(){
        $ca = $this->certification_applications('0');
        $this->assertTrue($ca->addCertification(Certifications::CERT_VOICE_P), "should have allowed adding certification to application");
        $this->assertFalse($ca->addCertification(Certifications::CERT_VOICE_P), "should have prevented adding certification to application");
    }

    /**
     * @depends testAddCertificationToCompletedApplicationWithPassResult
     * @depends testCertificationsArray
     */
    public function testDeleteCertification() {
        $ca = $this->certification_applications('0'); // complete application
        $this->assertTrue($ca->addCertification(Certifications::CERT_N_40_IN_50), "unable to add certification to application");
        
        $cert_ary = $ca->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertTrue(in_array(Certifications::CERT_N_40_IN_50, $cert_id_list), "should have found cert in list");

        $rv = $ca->deleteCertification(Certifications::CERT_N_40_IN_50);
        $this->assertTrue($rv, "not able to delete certification");

        $cert_ary = $ca->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertFalse(in_array(Certifications::CERT_N_40_IN_50, $cert_id_list), "should not have found cert in list");
    }

    /**
     * test of data cleanup after deleting this row
     * NOTE: this test is duplicated in CertificationApplicationsWithLabDataTest.php for dependencies
     */
    public function testDelete(){
        $app = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $app_id = $app->app_id;
        $this->assertTrue(is_numeric($app_id), " primary key is not numeric");

        // grab information from apps so we can query for it later to confirm delete
        $req_certs = $app->requested_certifications;

        $request_id = $req_certs[0]->request_id;
        $product_id = $app->product_id;
        $this->assertTrue(is_numeric($request_id), " primary key is not numeric");
        $this->assertTrue(is_numeric($product_id), " product_id is not numeric");

        $rc = RequestedCertifications::model()->findByPk($request_id); // from database
        $this->assertTrue($rc instanceof RequestedCertifications, "requested certification should exist");

        $tr = TestResults::model()->findByPk($request_id); // from database
        $this->assertTrue($tr instanceof TestResults, "requested certification should exist");

        $prod = Products::model()->findByPk($product_id); // from database
        $this->assertTrue($prod instanceof Products, "product should exist");

        $prod_certs = ProductCertifications::model()->findAll("cid='".Products::productId2Cid($product_id)."'");
        //print_r($prod_certs);
        $this->assertTrue($prod_certs[0] instanceof ProductCertifications, "product certification should exist");

        //$prod_certs = $prod->product_certifications;

       // delete the application
        $rv = $app->delete();
        $this->assertTrue($rv, "product was not deleted");


        // confirm that we did NOT delete from product_certifications
        // NOTE: this may change in future as logic gets more complete to detect when
        // deleting from product_certifications is safe

        //print "product_id = $product_id,";
        //print "cid = ".Products::productId2Cid($product_id)."\n";
        $prod_certs = ProductCertifications::model()->findAll("cid='".Products::productId2Cid($product_id)."'");
        //print_r($prod_certs);
        $this->assertTrue($prod_certs[0] instanceof ProductCertifications, "product certification should exist");


        // confirm app does not exist after delete
        $prod2 = CertificationApplications::model()->findByPk($app_id); // from database
        $this->assertFalse($prod2 instanceof CertificationApplications, "app should not exist");

        // confirm that related information does not exist either
        $rc = RequestedCertifications::model()->findByPk($request_id); // from database
        $this->assertFalse($rc instanceof RequestedCertifications, "requested certification should not exist");

        // confirm that related information does not exist either
        $tr = TestResults::model()->findByPk($request_id); // from database
        $this->assertFalse($tr instanceof TestResults, "test result should not exist");


    }

    public function testFindAllWithProductId(){
        $prod = $this->products('0');
        $prod_id = $prod->product_id;
        $ca = CertificationApplications::model()->findAll("product_id=:product_id", array('product_id'=>$prod_id));
        $this->assertTrue(is_array($ca), "not an array");
        $this->assertTrue(count($ca)>1, " not > 1 array element");
    }

    /**
     * test to see if affecting the published certification will change the product results
     */
    public function testChangeProductStreamResults() {
        $this->markTestSkipped(
              'updating the product from the application can cause infinite loops if both have afterSave() code that affect one another'
        );
        
        $ca = $this->certification_applications('0'); // from fixture, which is database since it resets it on load

        $tx24_old = $ca->certified_tx_spatial_stream_24;
        $rx24_old = $ca->certified_rx_spatial_stream_24;
        $tx50_old = $ca->certified_tx_spatial_stream_50;
        $rx50_old = $ca->certified_rx_spatial_stream_50;

        $tx24_new = 3;
        $rx24_new = 3;
        $tx50_new = 3;
        $rx50_new = 3;

        $this->assertGreaterThan((int)$tx24_old, (int)$tx24_new);
        $this->assertGreaterThan((int)$rx24_old, (int)$rx24_new);
        $this->assertGreaterThan((int)$tx50_old, (int)$tx50_new);
        $this->assertGreaterThan((int)$rx50_old, (int)$rx50_new);


        $ca->certified_tx_spatial_stream_24 = $tx24_new;
        $ca->certified_rx_spatial_stream_24 = $rx24_new;
        $ca->certified_tx_spatial_stream_50 = $tx50_new;
        $ca->certified_rx_spatial_stream_50 = $rx50_new;
        $rv = $ca->save();
        $this->assertTrue($rv, "unable to save stream changes");

        // load product results
        $prod = Products::model()->findByPk($ca->product_id);
        $this->assertTrue($prod instanceof Products);
        $this->assertEquals((int)$tx24_new, (int)$prod->certified_tx_spatial_stream_24);
        $this->assertEquals((int)$rx24_new, (int)$prod->certified_tx_spatial_stream_24);
        $this->assertEquals((int)$tx50_new, (int)$prod->certified_tx_spatial_stream_50);
        $this->assertEquals((int)$rx50_new, (int)$prod->certified_tx_spatial_stream_50);
        
    }
    
    /**
     * due to ambigious database design we can have the value 'NULL' string
     * for frequency_band_mode. We want to make sure this is always a blank string instead
     * to make sure the data matches the rest of the systems expectations
     */
    public function testValidateFrequencyBandMode() {
        $ca = $this->certification_applications('0');
        $ca->frequency_band_mode = 'NULL';
        $this->assertFalse($ca->save(), "should have prevented the string value 'NULL'");
        $ca->frequency_band_mode = '';
        $this->assertTrue($ca->save(), "should have allowed the empty string value ''");
        $ca->frequency_band_mode = 'concurrent';
        $this->assertTrue($ca->save(), "should have allowed the empty string value 'concurrent'");
        $ca->frequency_band_mode = 'selectable';
        $this->assertTrue($ca->save(), "should have allowed the string value 'selectable'");
    }
    
   
    /**
     * assure that user is unable to delete the last application of a product
     * this would imply deleting the product
     * 
     * @depends testRelationsProducts
     */
    public function testDeletePreventionOfOnlyApp(){
        //$this->markTestIncomplete(
//          'This test has not been implemented yet.'
  //      );

        $prod = $this->products('0');
        $apps = $prod->certification_applications;
        $this->assertEquals(2, count($apps));
        $app1 = $apps[0];
        $app2 = $apps[1];
        $this->assertTrue($app1->delete(), "should have allowed deleting app");
        $this->assertFalse($app2->delete(), "should have prevented deleting last app");
        
    }
    public function testPublishOnList() {
        $ary = Products::model()->publishOnList();
        $this->assertInternalType('array', $ary);
        $this->assertGreaterThan(0, count($ary));
    }

    /**
     * @depends testDelete
     */
    //public function testActivityLogDelete() {

    //}

    public function testTypeOptionList() {
        $ary = CertificationApplications::typeOptionList();
        $this->assertInternalType('array', $ary);
        
    }

    
    public function testTypeValidation() {
        $ca = $this->certification_applications('0');
        // bad data test #1
        $ca->certification_type = 'BAD DATA';
        $this->assertFalse($ca->save(), "should have prevented a bad type");
        
        // bad data test #2
        $ca->certification_type = CertificationApplications::TYPE_RECERT;
        $ca->recert_type_id = CertificationApplications::TYPE_NEW;
        $this->assertFalse($ca->save(), "should have prevented a bad combination: if recert_type_id is set, then only ok with recertification");
        
        // good data tests
        $ca->recert_type_id = null; // set back to a legal value
        $ca->certification_type = CertificationApplications::TYPE_NEW;
        $this->assertTrue($ca->save(), "should NOT have prevented an ok type error = ". print_r($ca->getErrors(), true));
        $ca->certification_type = CertificationApplications::TYPE_DEPENDENT;
        $this->assertTrue($ca->save(), "should NOT have prevented an ok type");
        $ca->certification_type = CertificationApplications::TYPE_ADDITIONAL;
        $this->assertTrue($ca->save(), "should NOT have prevented an ok type");
        $ca->certification_type = CertificationApplications::TYPE_TRANSFER;
        $this->assertTrue($ca->save(), "should NOT have prevented an ok type");
        $ca->certification_type = CertificationApplications::TYPE_RECERT;
        $ca->recert_type_id = CertificationApplications::RECERT_TYPE_ID_FIRMWARE_AFFECTING;
        $this->assertTrue($ca->save(), "should NOT have prevented an ok type setting recert");
    }
	
	public function testSplitParentAppIdValidation() {
		$ca1 = $this->certification_applications('0');
		$ca2 = $this->certification_applications('1');
		$this->assertTrue($ca1 instanceof CertificationApplications);
		$this->assertTrue($ca2 instanceof CertificationApplications);
		
		// test bad split parent app id
		$ca2->split_parent_app_id = 111111111;
		$this->assertFalse($ca2->save(), 'should have prevented bad split_parent_app_id');
		
		// test good split parent app id
		$ca2->split_parent_app_id = $ca1->app_id;
		$this->assertTrue($ca2->save(), 'should have allowed good split_parent_app_id');
	}
	
	/**
	 * test that setting of import_parent_app_id is working
	 */
	public function testImportParentAppIdValidation() {
		$p1 = $this->products('0');
		$p1->is_module = 1;
		$this->assertTrue($p1->save(), "not able to save product with is_module set");
		$ca_list = $p1->certification_applications;
		$ca1 = (is_array($ca_list)) ? $ca_list[1] : $ca_list;
		$this->assertTrue($ca1 instanceof CertificationApplications, "ca is not an instance of CertificationApplications ");
	
		$p2 = $this->products('1');
		$this->assertTrue($p2 instanceof Products, "p2 is not an instance of Products ");
	//return;	
		$ca2 = new CertificationApplications();
		//$ca2->product_id = 123;
		$ca2->product_id = $p2->product_id;
		$ca2->import_parent_app_id = $ca1->app_id;
		$this->assertTrue($ca2->save(), "should have been able to save changes to import parent app id");
#return;
		// now test the negative, not allowing a save when parent app is not a module
		$p1->is_module = 0;
		$this->assertTrue($p1->save(), "should have saved is_module value to product");
		$ca2->import_parent_app_id = $ca1->app_id;
		$this->assertFalse($ca2->save(), "should have been prevent save changes to import parent app id, parent product not a module");
		
		// now test the negative again, not allowing a save when parent app does not exist
		$ca2->import_parent_app_id = 99999999;
		$this->assertFalse($ca2->save(), "should have been prevent save changes to import parent app id, parent app id does not exist");
		
	}

    public function testGetCompanyContactsLikeNameList(){
        $name = 'wfa_member@wi-fi.org';
        $rows = CertificationApplications::getCompanyContactsLikeNameList($name);
        $this->assertInternalType('array', $rows);
        $this->assertGreaterThan(0, count($rows));
        foreach ($rows as $row){
            $this->assertEquals($name, $row['company_contact']);
        }
    }

    public function testStatusList() {
        $ary = CertificationApplications::statusList();
        $this->assertInternalType('array', $ary);
        $this->assertEquals(8, count($ary));
    }

    	
    /**
     * test whether changing the status from 7: Complete to anything else
     * will remove the certifications from the product, unless the application
     * is a re-certification or a transfer
     *
     * @depends testCertificationsArray
     */
    public function testPushBackAppStatusAffectsProductCerts() {

        $ca = $this->certification_applications('0');
        $this->assertEquals(CertificationApplications::STATUS_COMPLETE, $ca->status);
        $this->assertEquals(CertificationApplications::TYPE_NEW, $ca->certification_type);
        // get list of certifications for this app
        $app_certs = $ca->certificationsArray();
        $this->assertTrue(is_array($app_certs), "is not an array");
        $this->assertGreaterThan(1, count($app_certs), "number of certs in app is not > 1");

        // get list of certifications for this product
        $prod = Products::model()->findByPk($ca->product_id);
        $before_cert_rows = $prod->certificationsArray();

        // set status of completed application back to lab result entry
        $ca->status = CertificationApplications::STATUS_STEP4;
        $this->assertTrue($ca->save(), "unable to save status change");

        $after_cert_rows = $prod->certificationsArray();

        $before_cert_list = array();
        $after_cert_list = array();

        foreach($before_cert_rows as $row){
            $before_cert_list[] = $row['cert_id'];
        }
        foreach($after_cert_rows as $row){
            $after_cert_list[] = $row['cert_id'];
        }

        foreach($before_cert_list as $cert_id) {
            $this->assertFalse(in_array($cert_id, $after_cert_list), "cert_id=$cert_id should have been removed from product");
        }
    }

    public function testPreventIllegalRecertType(){
	}
    /**
     * NOTE this test is duplicated in CertificationApplicationsWithLabDataTest.php for dependencies
     */
    public function testGetActivityLogTableName() {
        $ca = $this->certification_applications('0');
        $this->assertEquals('applications', $ca->getActivityLogTableName());
    }
    /**
     * @depends testDelete
     * @depends testGetActivityLogTableName
     */
    public function testActivityLogDelete() {
        $ca = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $pk = $ca->app_id;

        $rows = $ca->getActivityLogRows();
        $this->assertInternalType('array', $rows, "should have been a returned array");
        $this->assertEquals(0, count($rows), "should have returned zero rows for activity log");

        $ca->delete();
        $rows = $ca->getActivityLogRows();
        $this->assertInternalType('array', $rows, "should have been a returned array");
        $this->assertEquals(1, count($rows), "should have returned 1 row for activity log");
        $this->assertEquals('Delete', $rows[0]['description']);

    }
	
public function testIsImported() {
	$p1 = $this->products('0');
	//$ca1 = $this->certification_applications('1');
	$p2 = $this->products('1');
	
	$ca_list1 = $p1->certification_applications;
	$ca1 = (is_array($ca_list1)) ? $ca_list1[0] : $ca_list1;
	
	$ca_list2 = $p2->certification_applications;
	$ca2 = (is_array($ca_list2)) ? $ca_list2[0] : $ca_list2;
	
	$p1->is_module = 1;
	$this->assertTrue($p1->save(), "unable to save due to errors".(print_r($p1->getErrors(), true)));
	
	$ca2->import_parent_app_id = $ca1->app_id;
	$this->assertTrue($ca2->save(), "unable to save due to errors".(print_r($ca2->getErrors(), true)));
	$this->assertTrue($ca2->isImported(), "should have detected app was imported");
	
	$ca2->import_parent_app_id = 0;
	
	$this->assertTrue($ca2->save(), "unable to save due to errors".(print_r($ca2->getErrors(), true)));
	$this->assertFalse($ca2->isImported(), "should have detected app was NOT imported");
    }


}
?>
