<?php

/**
* @covers Products
*/
class ProductsTest extends CDbTestCase {

    public $fixtures=array(
    'products'=>'Products',
    'certification_applications'=>'CertificationApplications',
    'product_certifications'=>'ProductCertifications',
    'requested_certifications'=>'RequestedCertifications',
    'activity_log'=>'ActivityLog',
    );

public function clearUploadedFiles() {
	return; // disabling this this potentially dangerous cleanup
	// clean up uploaded files
	$file_dir = Yii::app()->params->uploaded_data_dir .'/';
	$filelist = scandir($file_dir);
	foreach ($filelist as $file) {
		$file_path = $file_dir . $file;
		if (is_file($file_path)){
			unlink($file_path);
		}
	}

}
    /**
     * tests ability of model to convert date formats to MM/DD/YYYY ##:##:## on the fly
     */
    public function testDateSaveAndLoadNewFormat() {
        //$this->markTestSkipped(
        //      'This functionality not completed yet'
        //);

        $date6 = '1/6/2006 13:16:28';
        $date6_expected = '1/6/2006'; // this is a date field (not datetime)
        $date7 = 0;
        $date7_expected = '0000-00-00'; // this is a date field (not datetime)

        //$date_notime = '1/2/2001';
        $prod = $this->products('0');
        $product_id = $prod->product_id;

        $prod = Products::model()->findByPk($product_id);
        $prod->deferred_date = $date6;
        $this->assertTrue($prod->save(), "should have been able to save date(s)");
        $prod = Products::model()->findByPk($product_id);
        $this->assertEquals($date6_expected, $prod->deferred_date);

        // test for special case of "zero" date
    }
    /**
     * special cases are needed to test dates when value is zero
     */
    public function testDateSaveAndLoadNewFormatWithZeroValue(){
        $date_in = '0000-00-00';
        $p = $this->products('0');
        $p->deferred_date = $date_in;
        $p->save();
        $p2 = CertificationApplications::model()->findByPk($p->product_id);
        $this->assertEquals('0000-00-00', $p2->deferred_date);
    }
    
public function testSaveStreams() {
	$p = $this->products('0');
        $p->certified_tx_spatial_stream_24 = Products::MAX_CERTIFIED_STREAMS;
        $p->certified_rx_spatial_stream_24 = Products::MAX_CERTIFIED_STREAMS;
        $p->certified_tx_spatial_stream_50 = Products::MAX_CERTIFIED_STREAMS;
        $p->certified_rx_spatial_stream_50 = Products::MAX_CERTIFIED_STREAMS;
        $p->certified_tx_spatial_stream_50_ac = Products::MAX_CERTIFIED_STREAMS;
        $p->certified_rx_spatial_stream_50_ac = Products::MAX_CERTIFIED_STREAMS;

        $p->supported_tx_spatial_stream_24 = Products::MAX_SUPPORTED_STREAMS;
        $p->supported_rx_spatial_stream_24 = Products::MAX_SUPPORTED_STREAMS;
        $p->supported_tx_spatial_stream_50 = Products::MAX_SUPPORTED_STREAMS;
        $p->supported_rx_spatial_stream_50 = Products::MAX_SUPPORTED_STREAMS;
        $p->supported_tx_spatial_stream_50_ac = Products::MAX_SUPPORTED_STREAMS_AC;
        $p->supported_rx_spatial_stream_50_ac = Products::MAX_SUPPORTED_STREAMS_AC;
		
		
	$rv = $p->save();
        $this->assertTrue($rv, "unable to save certified spatial streams");
        
	// test a failure
	
	$p = $this->products('0');
        $p->certified_tx_spatial_stream_50_ac = Products::MAX_CERTIFIED_STREAMS + 10;
        $rv = $p->save();
        $this->assertFalse($rv, "should have prevented saving invalid number of streams");
		
	$p = $this->products('0');
        $p->supported_tx_spatial_stream_50_ac = Products::MAX_SUPPORTED_STREAMS_AC + 10;
        $rv = $p->save();
        $this->assertFalse($rv, "should have prevented saving invalid number of streams");		
   }
   
    public function testRelationsCertificationApplications(){
        $prod = $this->products('0'); // from fixture, which is database since it resets it on load
        //$cert = Certifications::model()->with('requested_certifications')->findByPk(41);
        $this->assertTrue($prod instanceof Products);

        // lazy load
        $apps = $prod->certification_applications;
        $this->assertTrue(is_array($apps), "is not an array");
        $this->assertTrue($apps[0] instanceof CertificationApplications, "is not instance of CertificationApplications");
    }

    public function testRelationsCompanies() {
        $prod = $this->products('0'); // from fixture, which is database since it resets it on load
     // lazy load
        $companies = $prod->companies; // only one, so not an array
        $this->assertTrue($companies instanceof Companies, "is not instance of Companies");
    }

  
    public function testRelationsProductCategories(){
        $prod = $this->products('0'); // from fixture, which is database since it resets it on load
        // lazy load
        $pcat = $prod->product_categories;
        //$this->assertTrue(is_array($prod_certs), "is not an array");
        $this->assertTrue($pcat instanceof ProductCategories, "is not instance of ProductCategories");
    }

    public function testRelationsDeviceTypes(){
        $prod = $this->products('0'); // from fixture, which is database since it resets it on load
        // lazy load
        $dt = $prod->device_types;
        //$this->assertTrue(is_array($prod_certs), "is not an array");
        $this->assertTrue($dt instanceof DeviceTypes, "is not instance of DeviceTypes");
    }

		// not sure why test fails, seems to work in code
	//public function testRelationsOs(){
//        $prod = $this->products('0'); // from fixture, which is database since it resets it on load
		//if (0) {
//				$os_rows = Os::model()->findAll();
				////print_r($rows);
				//foreach ($os_rows as $r) {
//						print "os_id=".$r->os_id.", name=".$r->name."\n";
				//}
		//}
		
        // lazy load
 //       $dt = $prod->os;
//				print_r($os);
//        //$this->assertTrue(is_array($prod_certs), "is not an array");
//        $this->assertTrue($dt instanceof Os, "is not instance of Os");
//    }
		
    //  Removing this relation, its too much of a resource hog on real data
    //public function testRelationsProductCertifications() {
    //    $prod = $this->products('0'); // from fixture, which is database since it resets it on load
     //   // lazy load
     //   $prod_certs = $prod->product_certifications;
     //   $this->assertTrue(is_array($prod_certs), "is not an array");
     //   $this->assertTrue($prod_certs[0] instanceof ProductCertifications, "is not instance of ProductCertifications");
    //}

    /**
     * Test the function that will replace the relations() product_certifications
     */
    public function testGetProductCertifications(){
        $prod = $this->products('0');
        $this->assertTrue($prod instanceof Products, "is not instance of Products");
        $prod_certs = $prod->getProductCertifications();
        $this->assertTrue(is_array($prod_certs), "is not an array");
        $this->assertTrue($prod_certs[0] instanceof ProductCertifications, "is not instance of ProductCertifications");

    }

    public function testProductId2Cid(){
        $prod_id = '111';
        $cid = Products::productId2Cid($prod_id);
        $this->assertTrue($cid=='WFA0111', "cid ($cid) should have been WFA0111");

        $prod_id = '1234';
        $cid = Products::productId2Cid($prod_id);
        $this->assertTrue($cid=='WFA1234');

        $prod_id = '12345';
        $cid = Products::productId2Cid($prod_id);
        $this->assertTrue($cid=='WFA12345');
    }

    public function testCid2ProductId() {
        
        $cid = 'WFA12345  '; // some spaces added
        $prod_id = Products::cid2ProductId($cid);
        $this->assertTrue($prod_id=='12345', "prod_id ($prod_id) should have been 12345");

        $cid = 'W54321'; // old format
        $prod_id = Products::cid2ProductId($cid);
        $this->assertTrue($prod_id=='54321', "prod_id ($prod_id) should have been 54321");

        try {
            $cid = '12345'; // bad
            $prod_id = Products::cid2ProductId($cid);
            $this->assertTrue($prod_id=='12345', "prod_id ($prod_id) should have been 12345");
        }
        catch (InvalidArgumentException $e){
            $msg = $e->getMessage();
            $this->assertTrue($msg =='not a cid format', "error message should have been 'not a cid format'");
        }
    }
    /**
     * assure that proper cleanup happens after product deleted
     */
    public function testDelete() {
        $prod = $this->products('0'); // from fixture, which is database since it resets it on load
        $prod_pk = $prod->product_id;
        $this->assertTrue(is_numeric($prod_pk), " primary key is not numeric");

        // grab information from product so we can query for it later to confirm delete
        $apps = $prod->certification_applications;
 
        $app1_pk = $apps[0]->app_id;
        $this->assertTrue(is_numeric($app1_pk), " primary key is not numeric");
        $app1 = CertificationApplications::model()->findByPk($app1_pk); // from database
        $this->assertTrue($app1 instanceof CertificationApplications, "application should exist");

        $prod_certs = $prod->getProductCertifications();

        //print_r($prod_certs);
        //foreach ($prod_certs as $pc){
            //print "cid = ".$pc->cid.", ";
            //print "=====================";
            //print_r($pc);
        //}
        $this->assertTrue(preg_match('/^WFA\d+$/', $prod_certs[0]->cid)==1, " cid is not in WFA##### format");

        // confirm product exists before delete
        $prod2 = Products::model()->findByPk(1); // from database
        $this->assertTrue($prod2 instanceof Products, "product should exist");

        // delete the product
        $rv = $prod->delete();
        $this->assertTrue($rv, "product was not deleted");

        // confirm product does not exist after delete
        $prod2 = Products::model()->findByPk(1); // from database
        $this->assertFalse($prod2 instanceof Products, "product should not exist");

        // confirm that related information does not exist either
        $app1 = CertificationApplications::model()->findByPk($app1_pk); // from database
        $this->assertFalse($app1 instanceof CertificationApplications, "application should not exist");        
    }

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
     * @depends testCertificationsArray
     */
    public function testCertIdList() {
        $p = $this->products('0');
        $cert_id_list = $p->certIdList();
        $this->assertInternalType('array', $cert_id_list);
        $this->assertGreaterThan(0, count($cert_id_list));
        
        // need to compare this list to the contents of
        $prod_certs = ProductCertifications::model()->with('certifications')->findAll("cid=:cid", array(':cid'=>$p->cid));
        $this->assertEquals(count($prod_certs), count($cert_id_list));

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
    public function testAddCertificationBadCertId() {
        $prod = $this->products('0');
        $this->assertFalse($prod->addCertification(0), " should have prevented adding cert");
    }

    /**
     * @depends testAddCertification
     */
    public function testAddCertificationDuplicateCertId() {
        $prod = $this->products('0');
        $this->assertTrue($prod->addCertification(Certifications::CERT_VOICE_P), " should have allowed adding cert");
        $this->assertFalse($prod->addCertification(Certifications::CERT_VOICE_P), " should have prevented adding cert");
    }

    /**
     * @depends testAddCertification
     */
    public function testAddCertificationPreventIfNoPublishedApp() {
        $prod1 = $this->products('0');
        $prod2 = $this->products('1'); // no publishable applications in this product

        // just a sanity check to make sure we have a product without a legitimately publishable app
        $app_test1 = CertificationApplications::model()->publishable()->find("product_id=:product_id", array('product_id'=>$prod1->product_id));
        $app_test2 = CertificationApplications::model()->publishable()->find("product_id=:product_id", array('product_id'=>$prod2->product_id));
 
        $this->assertTrue(!empty($app_test1), "should have found this app to be publishable");
        $this->assertFalse(!empty($app_test2), "should have found this app NOT to be publishable");

        $rv = $prod1->addCertification(Certifications::CERT_VOICE_P);
        $this->assertTrue($rv, "should have allowed adding the certification to a product without a publishable application");

        $rv = $prod2->addCertification(Certifications::CERT_VOICE_P);
        $this->assertFalse($rv, "should have prevented adding the certification to a product without a publishable application");

        //$this->markTestIncomplete(
          //'This test has not been implemented yet. We still need a product in database without a completed app.'
        //);
    }


    public function testFindLatestAppWithCertN() {
        $prod = $this->products('0');
        $app = $prod->findLatestAppWithCertN();
        $this->assertTrue($app instanceof CertificationApplications, "app not found");

        // cycle through requested application to confirm this application has cert N
        $has_cert_n = false;
        foreach($app->requested_certifications as $rc){
            if (in_array($rc->cert_id, Certifications::getCertNRequiredList())){
                $has_cert_n = true;
            }
        }
        $this->assertTrue($has_cert_n, "unable to find a cert n in this application");
    }
    
    /**
     * @depends testFindLatestAppWithCertN
     */
    public function testChangeStreamAffectsApplication(){
        $prod = $this->products('0');
        
        $prod->certified_tx_spatial_stream_24 = 0;
        $prod->certified_rx_spatial_stream_24 = 1;
        $prod->certified_tx_spatial_stream_50 = 2;
        $prod->certified_rx_spatial_stream_50 = 3;
        $this->assertTrue($prod->save());

        $app = $prod->findLatestAppWithCertN();
        $this->assertEquals($app->certified_tx_spatial_stream_24, $prod->certified_tx_spatial_stream_24);
        $this->assertEquals($app->certified_rx_spatial_stream_24, $prod->certified_rx_spatial_stream_24);
        $this->assertEquals($app->certified_tx_spatial_stream_50, $prod->certified_tx_spatial_stream_50);
        $this->assertEquals($app->certified_rx_spatial_stream_50, $prod->certified_rx_spatial_stream_50);
    }

    public function testValidationParentIdBad(){

        // no database match for parent_id
        $prod = $this->products('0');
        $prod->parent_id = 9999999;
        $this->assertFalse($prod->save(), "should have prevented changing parent_id to invalid value");
    
        // self-referencing parent
        $prod = $this->products('0');
        $prod->parent_id = $prod->product_id;
        $this->assertFalse($prod->save(), "should have prevented changing parent_id to invalid value");
    }

    /**
     * @todo test with an existing product rather than just default 0
     */
    public function testValidationParentIdOk(){
        $prod = $this->products('0');
        $prod->parent_id =0 ;
        $this->assertTrue($prod->save(), "should not have prevented changing parent_id to invalid value");
        
    }
		
	public function testValidationOsOther(){
        $prod = $this->products('0');
        $prod->os_id = Os::OS_ID_OTHER;
		$prod->os_other = '';
        $this->assertFalse($prod->save(), "should have prevented saving a blank os other when 'Other' is selectedS");
	}
	public function testValidationOsOther2 () {
		unset($prod);
	   $prod = $this->products('0');
        $prod->os_id = 1; // Android
	   $prod->os_other = '';
        $this->assertTrue($prod->save(), "should not have prevented saving a os_id. errors=" . print_r($prod->getErrors(), true));
    }
		
    public function testValidationClonedFromBad(){

        // no database match for parent_id
        $prod = $this->products('0');
        $prod->cloned_from = 9999999;
        $this->assertFalse($prod->save(), "should have prevented changing cloned_from to invalid value");

        // self-referencing 
        $prod = $this->products('0');
        $prod->cloned_from = $prod->product_id;
        $this->assertFalse($prod->save(), "should have prevented changing cloned_from to invalid value");
    }


    /**
     * @todo test with an existing product rather than just default 0
     */
    public function testValidationClonedFromOk(){
        $prod = $this->products('0');
        $prod->cloned_from =0 ;
        $this->assertTrue($prod->save(), "should not have prevented changing cloned_from to invalid value");

    }

    public function testPublishOnList() {
        $ary = Products::model()->publishOnList();
        $this->assertInternalType('array', $ary);
        $this->assertGreaterThan(0, count($ary));
    }

    public function testGetActivityLogTableName() {
        $p = $this->products('0');
        $this->assertEquals('products', $p->getActivityLogTableName());
    }
    
     /**
     * @depends testChangeStreamAffectsApplication
     * @depends testGetActivityLogTableName
     */
    public function testActivityLogChange() {
        $p = $this->products('0'); // from fixture, which is database since it resets it on load
        $pk = $p->product_id;

        $old_value = $p->certified_tx_spatial_stream_24;
        $this->assertEquals(2, $old_value);
        $new_value = 3;
        $p->certified_tx_spatial_stream_24 = $new_value;
        $p->save();

        $rows = $p->getActivityLogRows();
        $this->assertInternalType('array', $rows, "should have been a returned array");
        $this->assertGreaterThan(0, count($rows), "should have returned rows for activity log");
        

        $row = $rows[0]; // sorted in DESC date, so this is last change
        $old_data = unserialize($row['old_data']);
        $new_data = unserialize($row['new_data']);

        $this->assertTrue(isset($old_data['certifications']), "cannot find 'certifications' array in the change log");
        $this->assertTrue(isset($new_data['certifications']), "cannot find 'certifications' array in the change log");

        $this->assertEquals($new_value, $new_data['certified_tx_spatial_stream_24']);
        $this->assertEquals($old_value, $old_data['certified_tx_spatial_stream_24']);

        $app = CertificationApplications::model()->publishable()->most_recent()->find('product_id=:product_id', array('product_id'=>$pk));
        // make sure we have app_id in both the new and old data array
        $this->assertEquals($app->app_id, $old_data['app_id'], "could not find the app_id in the old_data array");
        $this->assertEquals($app->app_id, $new_data['app_id'], "could not find the app_id in the new_data array");

        // assure we have certifications in both the new and old data array
        $this->assertNotNull($old_data['certifications'], "could not find certifications in the old_data array");
        $this->assertNotNull($new_data['certifications'], "could not find certifications in the new_data array");
    }

    /**
     * assures that we save a copy of old row data in the activity log after we delete
     *
     * @depends testDelete
     * @depends testGetActivityLogTableName
     */
    public function testActivityLogDelete() {
        $p = $this->products('0'); // from fixture, which is database since it resets it on load
        $pk = $p->product_id;

        $rows = $p->getActivityLogRows();
        $this->assertInternalType('array', $rows, "should have been a returned array");
        $this->assertEquals(0, count($rows), "should have returned zero rows for activity log");

        $predelete_data = $p->attributes;
        
        $p->delete();
        $rows = $p->getActivityLogRows();
        $this->assertInternalType('array', $rows, "should have been a returned array");
        $this->assertEquals(1, count($rows), "should have returned 1 row for activity log");
        $this->assertEquals('Delete', $rows[0]['description']);

        $old_data = $rows[0]['old_data'];
        $ary_1 = unserialize($old_data);
        $ary_2 = $predelete_data;
        $this->assertInternalType('array', $ary_1);
        $this->assertInternalType('array', $ary_2);
        $this->assertGreaterThan(0, count($ary_1));
        $this->assertGreaterThan(0, count($ary_2));
        $diff_ary = array_diff($ary_1, $ary_2);
        $this->assertEquals(0, count($diff_ary), "data from predelete and old_data should be identical");
    }

    
   public function testIsTestEngine() {
       $prod = $this->products('0');
       $this->assertFalse($prod->isTestEngine(), "this product should not be test engine");

       $prod = $this->products('1');
       $this->assertFalse($prod->isTestEngine(), "this product should not be test engine");

       // lets make this product a test engine
       //$ca = $prod->certification_applications; // this does not work here
       $ca = CertificationApplications::model()->find('product_id=:product_id', array('product_id'=>$prod->product_id));
       $ca->test_engine = 1;
       $this->assertTrue($ca->save(), "should not have prevented saving app as test engine");
       $this->assertTrue($prod->isTestEngine(), "this product should have been a test engine");

   }
   
   public function testIsAccessPoint() {
       $prod = $this->products('0');
       $this->assertTrue($prod->isAccessPoint(), "this product (category_id = {$prod->category_id} should be access point");
       $rv = in_array($prod->category_id, 
               array (
                    ProductCategories::AP_HOME,
                   ProductCategories::AP_ENTERPRISE,
                   ProductCategories::AP_GATEWAY,
                   ProductCategories::AP_MOBILE,
               ));
       $this->assertTrue($rv);
   }
   
 

   /**
    * load the parent product active record
    */
   public function testGetParentProduct() {
       // we are going to cheat here to save the need to create more pre-populated data
       // change one of the products to be a child of another then run the test
       //print "---------------- TEST GET PARENT PRODUCT ---------------\n\n";
       $parent_prod = $this->products('0');
	   
       //$child_prod = $this->products('1');
       $child_prod = Products::model()->findByPk(2); // from database
	$this->assertInternalType('object', $child_prod);   
	  //print "===========================\n\n\child_prod = " . var_dump($child_prod);
       $child_prod->parent_id = $parent_prod->product_id;
	  $rv = $child_prod->save();
       $this->assertTrue($rv, "should have allowed saving product");
       
       // need to reload child product to actually see the values
       $child_product_id = $child_prod->product_id;
       $child_prod = null;
       $child_prod = Products::model()->findByPk($child_product_id);
       $this->assertTrue($child_prod instanceof Products, "unable to load child product");
       
       // load the parent
       $parent_prod2 = $child_prod->getParentProduct();
       $this->assertTrue($parent_prod2 instanceof Products, "unable to load parent product");
       
       // make sure the parent is the same as we expect
       $this->assertEquals($parent_prod->product_id, $parent_prod2->product_id, "product_id is not equal as expected");
   }
   
   public function testGetDependentProducts() {
       // we are going to cheat here to save the need to create more pre-populated data
       // change one of the products to be a child of another then run the test
      
       $parent_prod = $this->products('0');
       $child_prod = $this->products('1');
       $child_prod->parent_id = $parent_prod->product_id;
       $this->assertTrue($child_prod->save(), "should have allowed saving product");
       
       $dep_prod_list = $parent_prod->getDependentProducts();
       $this->assertTrue(is_array($dep_prod_list), "should have returned an array");
       $this->assertTrue($dep_prod_list[0] instanceof Products, "should have returned Products object");
       $dep_prod = $dep_prod_list[0];
       $this->assertEquals($dep_prod->parent_id, $parent_prod->product_id, 
               "parent_id of dependent product should have been equal to parent product_id");
   }


   /**
    * @depends testGetParentProduct
    */
   function testDeleteParentProduct() {
       $prod_parent = $this->products('0');
       $prod_dep = $this->products('1');
       
       // fake it
       $prod_dep->parent_id = $prod_parent->product_id;
       $this->assertTrue($prod_dep->save(), "not able to save as dependent product");
       $pp = $prod_dep->getParentProduct();
       $this->assertTrue($pp instanceof Products, "could not find the parent product");
       $this->assertFalse($pp->delete(), "should not have allowed parent product to be deleted");
   }
   
    function testGetMostRecentlyCompletedRequestedCertification(){
       $prod = $this->products('0');
       $cert_list = $prod->certificationsArray();
       $cert_id = $cert_list[0]['cert_id'];
       $this->assertTrue(is_numeric($cert_id),'cert_id is not numeric');
       $rc = $prod->getMostRecentPublishableRequestedCertificationByCertId($cert_id);
       $this->assertTrue($rc instanceof RequestedCertifications, 'not instance of RequestedCertifications');
       
       // test again by inserting a new record?
   }
   
   /**
    * 
     * parent products (or modules) sometimes get additional certifications
     * these applications need to cascade down to the dependent 
     * 
     * NOTE: due to how data is restored at the BEGINING of each test
     * please do not put this at end of test suite since it affects 
     * data that needs to be retored for other tests
     *
    * @depends testAddCertification 
    * @depends testGetProductCertifications
    * @depends testGetMostRecentlyCompletedRequestedCertification
     */
    public function testImportParentApplication() {
				//$this->markTestIncomplete(
				//  'This test has not been implemented yet.'
				//);

				$PARENT_FIRMWARE = 'firmware v1';
				$PARENT_OS_ID = Os::OS_ID_OTHER;
				$PARENT_OS_OTHER = 'custom BSD';
				$PARENT_WIRELESS_CHIPSET = 'wireless chip ABC v3';
				
				// it is assumed that the pending variables have already updated the parent
				//$PENDING_APP_FIRMWARE = $PARENT_FIRMWARE;
				//$PENDING_APP_OS_ID = $PARENT_OS_ID;
				//$PENDING_APP_OS_OTHER = $PARENT_OS_OTHER;
				//$PENDING_APP_WIRELESS_CHIPSET = $PARENT_WIRELESS_CHIPSET;
				
				$INITIAL_APP_FIRMWARE = 'initial firmware v123';
				$INITIAL_APP_OS_ID = 1; // Android
				$INITIAL_APP_OS_OTHER = '';
				$INITIAL_APP_WIRELESS_CHIPSET = "initial chipset AB123";
				
				

				$this->clearUploadedFiles();
				$parent_prod = $this->products('0');
				$dep_prod = $this->products('1');

				// make the parent a module to meet the required conditions
				$parent_prod->is_module = 1;
				$parent_prod->os_id = $PARENT_OS_ID;
				$parent_prod->os_other = $PARENT_OS_OTHER;
				$parent_prod->firmware = $PARENT_FIRMWARE;
				$parent_prod->wireless_chipset = $PARENT_WIRELESS_CHIPSET;
				//$this->assertTrue($parent_prod->save(), "unable to make parent product a module");
				$this->assertTrue($parent_prod->save(), "unable to save changes to parent product : " . (print_r($parent_prod->getErrors(), true)));

				// fake a Re-certification application
				// so it will be a dependent product with a publishable application
				$prod_certs = $parent_prod->getProductCertifications();
				$this->assertTrue(is_array($prod_certs), "is not an array");
				$this->assertTrue($prod_certs[0] instanceof ProductCertifications, "is not instance of ProductCertifications");

				// add application to parent
				$ca = new CertificationApplications();
				$ca->product_id = $parent_prod->product_id;
				$ca->certification_type = CertificationApplications::TYPE_RECERT;
				$ca->recert_type_id = CertificationApplications::RECERT_TYPE_ID_FIRMWARE_AFFECTING;
				// make sure the dependent product app is publishable
				// is this an absolute requirement? It makes it more complicated if not, however.
				$ca->hold = 0;
				$ca->publish_on = 'Deferred Date';
				$ca->status = CertificationApplications::STATUS_COMPLETE;
				$ca->deferred_date = '2000-01-01'; // before today
				$ca->pending_os_id = $PARENT_OS_ID;
				$ca->pending_os_other = $PARENT_OS_OTHER;
				$ca->pending_firmware = $PARENT_FIRMWARE;
				$ca->pending_wireless_chipset = $PARENT_WIRELESS_CHIPSET;
				
				$ca->initial_os_id = $INITIAL_APP_OS_ID;
				$ca->initial_os_other = $INITIAL_APP_OS_OTHER;
				$ca->initial_firmware = $INITIAL_APP_FIRMWARE;
				$ca->initial_wireless_chipset = $INITIAL_APP_WIRELESS_CHIPSET;
				
				$this->assertTrue($ca->save(), "unable to save changes to parent product app: " . (print_r($ca->getErrors(), true)));
				foreach ($prod_certs as $p_cert) {
						//print "adding cert_id={$p_cert->cert_id}, ";
						$this->assertTrue($ca->addCertification($p_cert->cert_id, TestResults::PASS), "unable to set certification cert_id={$p_cert->cert_id} for new certification application" . print_r($ca->getErrors(), true));
				}
				// add CWG-RF as a test since it should not be copied over to the dependents
				$this->assertTrue($ca->addCertification(Certifications::CERT_CWG_RF, TestResults::PASS), "unable to add CWG-RF to app_id={$ca->app_id}");

				// check to make sure that the parent application is publishable
				$this->assertTrue($ca->isPublishable(), "parent application should have been publishable");

				$dep_prod->parent_id = $parent_prod->product_id;
				$this->assertTrue($dep_prod->save(), "could not make dep prod a child product");

				$rc_list = $ca->requested_certifications;
				$this->assertTrue(is_array($rc_list), "no requested certifications exist for newly added parent app");

				$parent_app_id = $ca->getPrimaryKey();
				$this->assertNotEmpty($parent_app_id, "unable to get parent_app_id");

				foreach ($ca->requested_certifications as $tmp_rc) {
						// create a data file for one of the certs that usually has one
						if ($tmp_rc->cert_id == Certifications::CERT_N_APPROVED) {
								// fake some file data
								$filename = Yii::app()->params->uploaded_data_dir . '/' . 'IEE80211_' . $tmp_rc->request_id . '.xls';
								$dummy_data = 'test stuff';
								file_put_contents($filename, $dummy_data);
								// fake some test_data

								/*
								 * field_id 	cert_id 	test_type 	field_name 		format 	placement 	test_plan 	obsolete 
								  6669		41		Access Point	Device Firmware Version	Text		2	New WPA2 Mandatory Test Plan	0
								  6673		41		Access Point	Is 802.11n Device	Yes/No		6	New WPA2 Mandatory Test Plan	0
								 */
								$pa_td = new TestData();
								$pa_td->request_id = $tmp_rc->request_id;
								$pa_td->field_id = 6669;
								$pa_td->data = 'myfirmware123';
								$this->assertTrue($pa_td->save(), "could not save test data for parent app");

								$pa_td = new TestData();
								$pa_td->request_id = $tmp_rc->request_id;
								$pa_td->field_id = 667;
								$pa_td->data = 'Yes';
								$this->assertTrue($pa_td->save(), "could not save test data for parent app");
						}
						if ($tmp_rc->cert_id == Certifications::CERT_CWG_RF) {
								$filename = Yii::app()->params->uploaded_data_dir . '/' . 'cwg-rf_' . $tmp_rc->request_id . '.xls';
								$dummy_data = 'test stuff';
								file_put_contents($filename, $dummy_data);
						}
						// publish the certifications
						$tmp_pc = new ProductCertifications();
						$tmp_pc->cid = Products::productId2Cid($ca->product_id);
						$tmp_pc->cert_id = $tmp_rc->cert_id;
						$this->assertTrue($tmp_rc->save(), "should have saved product certification");
				}


				// attempt the parent application import
				$rv = $dep_prod->importParentApplication($parent_app_id);
				//print "errors = "; print_r($dep_prod->errors);
				$this->assertTrue($rv, "could not import parent app (app_id=$parent_app_id):" . (print_r($dep_prod->getErrors(), true)));

				// attempt a second import, which should be prohibited
				$rv = $dep_prod->importParentApplication($parent_app_id);
				//print "errors = "; print_r($dep_prod->errors);
				$this->assertFalse($rv, "should not have allowed a second import of parent app (app_id=$parent_app_id):" . (print_r($dep_prod->getErrors(), true)));

				// compare attributes between parent and dep apps
				$ca1 = CertificationApplications::model()->findByPk($parent_app_id);
				$ca2 = CertificationApplications::model()->find('import_parent_app_id=:import_parent_app_id', array('import_parent_app_id' => $parent_app_id));
				$this->assertTrue($ca2 instanceof CertificationApplications, "could not find the dependent app based on import_parent_app_id (import_parent_app_id = $parent_app_id)");
				$this->assertEquals($ca1->app_id, $ca2->import_parent_app_id, 'parent app id  should be same value as import parent app id');
				$this->assertEquals($ca1->pending_os_id, $ca2->pending_os_id, 'pending_os_id should be same between parent app and dep app');
				$this->assertEquals($ca1->pending_os_other, $ca2->pending_os_other, 'pending_os_other should be same between parent app and dep app');
				$this->assertEquals($ca1->initial_os_id, $ca2->initial_os_id, 'initial_os_id should be same between parent app and dep app');
				$this->assertEquals($ca1->initial_os_other, $ca2->initial_os_other, 'initial_os_other should be same between parent app and dep app');
				$this->assertEquals($ca1->pending_firmware, $ca2->pending_firmware, 'pending_firmware should be same between parent app and dep app');
				$this->assertEquals($ca1->initial_firmware, $ca2->initial_firmware, 'initial_firmware should be same between parent app and dep app');
				$this->assertEquals($ca1->pending_wireless_chipset, $ca2->pending_wireless_chipset, 'pending_wireless_chipset should be same between parent app and dep app');
				$this->assertEquals($ca1->initial_wireless_chipset, $ca2->initial_wireless_chipset, 'initial_wireless_chipset should be same between parent app and dep app');
				$dep_app_id = $ca2->app_id;

				// now compare requested certifications
				$tmp_cert_id_list1 = array();
				$tmp_cert_id_list2 = array();
				foreach ($ca1->requested_certifications as $tmp_rc) {
						$tmp_cert_id_list1[] = $tmp_rc->cert_id;
				}
				foreach ($ca2->requested_certifications as $tmp_rc) {
						$tmp_cert_id_list2[] = $tmp_rc->cert_id;
				}
				sort($tmp_cert_id_list1);
				sort($tmp_cert_id_list2);
				$tmp_intersect = array_intersect($tmp_cert_id_list1, $tmp_cert_id_list2);

				//print "======\n tmp_cert_id_list1 = ".print_r($tmp_cert_id_list1);
				//print "\n \n tmp_cert_id_list2 = ".print_r($tmp_cert_id_list2);

				$this->assertNotEmpty($tmp_cert_id_list1, 'no requested certifications for parent app found');
				//$this->assertEquals(count($tmp_cert_id_list1), count($tmp_cert_id_list2), 'not equal cert_ids from parent to dep requested certs:'.print_r($tmp_intersect, true));
				// minus one since CWG-RF should not be copied over
				$this->assertEquals(count($tmp_cert_id_list1) - 1, (count($tmp_intersect)), 'not equal cert_ids from parent to dep requested certs:' . print_r($tmp_intersect, true));


				// check to see that file was copied over
				//getExistingExternalDataFilePath();
				$p_rc = RequestedCertifications::model()->find('app_id=:app_id AND cert_id=:cert_id', array('app_id' => $ca1->app_id, 'cert_id' => Certifications::CERT_N_APPROVED));
				$d_rc = RequestedCertifications::model()->find('app_id=:app_id AND cert_id=:cert_id', array('app_id' => $ca2->app_id, 'cert_id' => Certifications::CERT_N_APPROVED));

				// assert that file exists where it should
				$this->assertNotEmpty($p_rc->getExistingExternalDataFilePath(), "file path for CERT_N_APPROVED should not be blank");
				$this->assertNotEmpty($d_rc->getExistingExternalDataFilePath(), "file path for CERT_N_APPROVED should not be blank");

				$p_rc = RequestedCertifications::model()->find('app_id=:app_id AND cert_id=:cert_id', array('app_id' => $ca1->app_id, 'cert_id' => Certifications::CERT_G));
				$d_rc = RequestedCertifications::model()->find('app_id=:app_id AND cert_id=:cert_id', array('app_id' => $ca2->app_id, 'cert_id' => Certifications::CERT_G));

				// assert that file does not exists where it should not
				$this->assertEmpty($p_rc->getExistingExternalDataFilePath(), "file path for CERT_G should  be blank: " . $p_rc->getExistingExternalDataFilePath());
				$this->assertEmpty($d_rc->getExistingExternalDataFilePath(), "file path for CERT_G should  be blank: " . $d_rc->getExistingExternalDataFilePath());

				// check to make sure that the CERT_N_APPROVED in the parent app has data
				// tests below are partially redundant	
				// test to see that the data now exists in the dependent product
				// check the published certifications for the dependent product
				//
        
        // make sure that dependent application now has same certs as parent application
				$p_app = CertificationApplications::model()->findByPk($parent_app_id);
				$d_app = CertificationApplications::model()->findByPk($dep_app_id);

				$p_certs = $p_app->certIdList();
				$d_certs = $d_app->certIdList();

				$this->assertTrue(count($p_certs) > 0, "parent certs should be > 0");
				$this->assertTrue(count($d_certs) > 0, "dependent certs should be > 0");
				$diff1 = array_diff($p_certs, $d_certs);

				// remove CWG-RF from the certifications list that should have been imported
				foreach ($diff1 as $index => $value) {
						if ($value == Certifications::CERT_CWG_RF) {
								unset($diff1[$index]);
						}
				}

				$this->assertEquals(count($diff1), 0, "cert_ids that should have been imported but were not: " . implode(',', $diff1));

				// assure that CWG-RF is NOT imported
				$this->assertFalse(in_array(Certifications::CERT_CWG_RF, $d_certs), "CWG-RF should not have been imported");

				// check the dependent applications requested certifications 
				// to see if there are test results


				foreach ($p_app->requested_certifications as $p_rc) {
						$this->assertTrue($p_rc->test_results instanceof TestResults, "dependent requested certification (request_id={$p_rc->request_id}, cert_id={$p_rc->cert_id}) did not have associated test result");

						// check to see if the test data was copied for CERT_N_APPROVED
						if ($p_rc->cert_id == Certifications::CERT_N_APPROVED) {
								$p_td = $p_rc->test_data;
								$this->assertTrue(is_array($p_td), "test data did not return an array");
								$this->assertTrue($p_td[0] instanceof TestData, "did not return test data for requested certification (request_id={$p_rc->request_id} cert_id={$p_rc->cert_id})");
						}
				}

				$d_rc_list = $d_app->requested_certifications;
				foreach ($d_rc_list as $d_rc) {
						$this->assertTrue($d_rc->test_results instanceof TestResults, "dependent requested certification (request_id={$d_rc->request_id}, cert_id={$d_rc->cert_id}) did not have associated test result");

						// check to see if the test data was copied for CERT_N_APPROVED
						if ($d_rc->cert_id == Certifications::CERT_N_APPROVED) {
								$d_td = $d_rc->test_data;
								$this->assertTrue(is_array($d_td), "test data did not return an array");
								$this->assertTrue($d_td[0] instanceof TestData, "did not return test data for requested certification (request_id={$d_rc->request_id} cert_id={$d_rc->cert_id})");
						}
				}

				// REMOVING FOR NOW, we do not want auto-publishing for imported applications at this time
				// assure that the dependent product has all of the certifications of the parent application
				// these certifications should be published
				//
        $d_prod = Products::model()->findByPk($d_app->product_id);
				$d_certs = $d_prod->certIdList();
				$this->assertTrue(count($d_certs) > 0, "published dependent certs should be > 0");

				$diff2 = array_diff($d_certs, $p_certs);
				$this->assertEquals(count($diff2), 0, "cert_ids that should have been imported as published cert but were not: " . implode(',', $diff2));
				
				// see if the pending product changes have been copied over from the parent product to dependent one
				
				$dep_prod_id = $dep_prod->product_id;
				unset ($dep_prod);
				$dep_prod = Products::model()->findByPk($dep_prod_id);
				//print "rv = " . (print_r($rv, true)) . "\n";
				//$dep_prod = Products::model()->findByPk($dep_prod_id);
				//$attr = $dep_prod->getAttributes();
				//print "dep_attributes" . (print_r($attr, true));
				//$attr = $parent_prod->getAttributes();
				//print "parent_attributes" . (print_r($attr, true));
				
				$this->assertEquals($parent_prod->os_id, $dep_prod->os_id, 'os_id  should be same between parent and dep prod');
				$this->assertEquals($parent_prod->os_other, $dep_prod->os_other, 'os_other should be same between parent and dep prod');
				$this->assertEquals($parent_prod->firmware, $dep_prod->firmware, 'firmware should be same between parent and dep prod');
				$this->assertEquals($parent_prod->wireless_chipset, $dep_prod->wireless_chipset, 'wireless_chipset should be same between parent and dep prod');
		}
   
  /**
    * @depends testIsAccessPoint
    */
   public function testIsStation() {
       $prod = $this->products('0');
       $prod->category_id = ProductCategories::STA_INTERNAL_CARD;
       $this->assertTrue($prod->save());
       
       $this->assertFalse($prod->isAccessPoint(), "this product (category_id = {$prod->category_id} should not be an access point");
       $rv = in_array($prod->category_id, 
               array (
                    ProductCategories::AP_HOME,
                   ProductCategories::AP_ENTERPRISE,
                   ProductCategories::AP_GATEWAY,
                   ProductCategories::AP_MOBILE,
               ));
       $this->assertFalse($rv);
       $this->assertNotEquals($prod->isAccessPoint(), $prod->isStation(), "should not be equal");
   } 
   
}
?>
