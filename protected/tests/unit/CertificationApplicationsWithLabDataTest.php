<?php
/*
 * unit test for model CerificationApplications
 * and open the template in the editor.
 */

class CertificationApplicationsWithLabDataTest extends CDbTestCase {

    public $fixtures=array(
    'certification_applications'=>'CertificationApplications',
    'product_certifications'=>'ProductCertifications',
    'products'=>'Products',
    'requested_certifications'=>'RequestedCertifications',
    'test_results'=>'TestResults',
    //'activity_log'=>'ActivityLog', // just blanked out
    'test_data'=>'TestData',
    );

    /**
     * see if changing the streams in an application correctly updates the lab results
     */
    public function testChangeStreamAffectsLabResults(){
        $ca = $this->certification_applications('0'); // from fixture, which is database since it resets it on load

        // get the existing values
        // NOTE: these are data dependent, if the data in the tables change this may need an update
        // fixtures are supposed to keep things stable, fortunately.

        //print "-- app_id=".$ca->app_id;

        $td1 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_TX_STREAMS_24_CERT_N_APPROVED);
        $this->assertTrue($td1 instanceof TestData, "not instance of TestData, (not found?)");
        //print "-- td1->data_id=".$td1->data_id."\n";
        //print "-- td1->data=".$td1->data."\n";

        $td2 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_RX_STREAMS_24_CERT_N_APPROVED);
        $this->assertTrue($td2 instanceof TestData, "not instance of TestData, (not found?)");
        //print "-- td2->data_id=".$td2->data_id."\n";
        //print "-- td2->data=".$td2->data."\n";

        $td3 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_TX_STREAMS_50_CERT_N_APPROVED);
        $this->assertTrue($td3 instanceof TestData, "not instance of TestData, (not found?)");
        //print "-- td3->data_id=".$td3->data_id."\n";
        //print "-- td3->data=".$td3->data."\n";

        $td4 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_RX_STREAMS_50_CERT_N_APPROVED);
        $this->assertTrue($td4 instanceof TestData, "not instance of TestData, (not found?)");
        //print "-- td4->data_id=".$td4->data_id."\n";
        //print "-- td4->data=".$td4->data."\n";

        $this->assertEquals($td1->data, $ca->certified_tx_spatial_stream_24);
        $this->assertEquals($td2->data, $ca->certified_rx_spatial_stream_24);
        $this->assertEquals($td3->data, $ca->certified_tx_spatial_stream_50);
        $this->assertEquals($td4->data, $ca->certified_rx_spatial_stream_50);

        $ca->certified_tx_spatial_stream_24 = 0;
        $ca->certified_rx_spatial_stream_24 = 1;
        $ca->certified_tx_spatial_stream_50 = 2;
        $ca->certified_rx_spatial_stream_50 = 3;
        $rv = $ca->save();
        $this->assertTrue($rv, "unable to save stream changes");

        // the updates should have updated the lab results

        $td1 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_TX_STREAMS_24_CERT_N_APPROVED);
        $this->assertTrue($td1 instanceof TestData, "not instance of TestData, (not found?)");
        //print "-- td1->data_id=".$td1->data_id."\n";
        //print "-- td1->data=".$td1->data."\n";

        $td2 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_RX_STREAMS_24_CERT_N_APPROVED);
        $this->assertTrue($td2 instanceof TestData, "not instance of TestData, (not found?)");
        //print "-- td2->data_id=".$td2->data_id."\n";
        //print "-- td2->data=".$td2->data."\n";

        $td3 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_TX_STREAMS_50_CERT_N_APPROVED);
        $this->assertTrue($td3 instanceof TestData, "not instance of TestData, (not found?)");
        //print "-- td3->data_id=".$td3->data_id."\n";
        //print "-- td3->data=".$td3->data."\n";

        $td4 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_RX_STREAMS_50_CERT_N_APPROVED);
        $this->assertTrue($td4 instanceof TestData, "not instance of TestData, (not found?)");
        //print "-- td4->data_id=".$td4->data_id."\n";
        //print "-- td4->data=".$td4->data."\n";

        $this->assertEquals($td1->data, $ca->certified_tx_spatial_stream_24);
        $this->assertEquals($td2->data, $ca->certified_rx_spatial_stream_24);
        $this->assertEquals($td3->data, $ca->certified_tx_spatial_stream_50);
        $this->assertEquals($td4->data, $ca->certified_rx_spatial_stream_50);

    }

    public function testRelationsRequestedCertifications(){
        $cert_app = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $this->assertTrue($cert_app instanceof CertificationApplications);
        // lazy load
        $req_certs = $cert_app->requested_certifications;
        $this->assertTrue(is_array($req_certs), "is not an array");
        $this->assertTrue($req_certs[0] instanceof RequestedCertifications, "is not instance of RequestedCertifications");

    }

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
     * test of data cleanup after deleting this row
     * NOTE: this test is duplicated in CertificationApplicationsTest.php for dependencies
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

   /**
     *
     * @depends testCertificationsArray
     */
    public function testDeleteCertN() {
        $ca = $this->certification_applications('0'); // complete application

        $cert_ary = $ca->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertTrue(in_array(Certifications::CERT_N_APPROVED, $cert_id_list), "should have found cert in list");

        $rv = $ca->deleteCertification(Certifications::CERT_N_APPROVED);
        $this->assertTrue($rv, "not able to delete certification");

        $cert_ary = $ca->certificationsArray();
        $cert_id_list = array();
        foreach ($cert_ary as $row){
            $cert_id_list[]=$row['cert_id'];
        }
        $this->assertFalse(in_array(Certifications::CERT_N_APPROVED, $cert_id_list), "should not have found cert in list");
    }

    /**
     * @depends testCertificationsArray
     */
    public function testSetCertifications(){
        $ca = $this->certification_applications('0'); // complete application

        $set_id_list = array();
        $set_id_list[] = Certifications::CERT_B;
        $set_id_list[] = Certifications::CERT_A;
        $set_id_list[] = Certifications::CERT_G;
        $set_id_list[] = Certifications::CERT_VOICE_P;

        $cert_ary = $ca->certificationsArray();
        $this->assertNotEquals(count($cert_ary), count($set_id_list), "counts of certifications should not match the count of the input list");

        $rv = $ca->setCertifications($set_id_list);
        $this->assertTrue($rv, "unable to set certifications");

        $cert_ary = $ca->certificationsArray();
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
     * MARKED FOR DELETION - not sure why it's needed
     * 
     * @depends testSetCertifications
     * 
     * this will test that the test_result status will be different
     * depending on the status of the applications NOT_TESTED if before lab entry
     * and PASS otherwise
     */
    /*
    public function testSetCertificationsHasStatusBasedTestResult(){

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        $ca = $this->certification_applications('0'); // complete application

        $set_id_list = array();
        $set_id_list[] = Certifications::CERT_B;
        $set_id_list[] = Certifications::CERT_A;
        $set_id_list[] = Certifications::CERT_G;
        $set_id_list[] = Certifications::CERT_VOICE_P;
    }
     * 
     */

    
    /**
     * @depends testChangeStreamAffectsLabResults
     */
    public function testMaxTestedStreamsAllowed() {
        $this->assertEquals(3, CertificationApplications::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS);
        $ca = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $ca = CertificationApplications::model()->findByPk($ca->app_id); // need to reload since fixture is not reset
        //$ca->certified_tx_spatial_stream_24 = (CertificationApplications::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS + 1);
        $ca->certified_tx_spatial_stream_24 = 22;
        $this->assertFalse($ca->save(), "should not have been able to update");

        $ca->certified_tx_spatial_stream_24 = 3;
        $this->assertTrue($ca->save(), "should have been able to update");

        $td1 = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_TX_STREAMS_24_CERT_N_APPROVED);
        $this->assertTrue($td1 instanceof TestData, "not instance of TestData, (not found?)");
        $this->assertEquals(CertificationApplications::MAX_TESTABLE_STREAMS_IN_LAB_RESULTS, $td1->data);
    }


    public function testGetActivityLogTableName() {
        $ca = $this->certification_applications('0');
        $this->assertEquals('applications', $ca->getActivityLogTableName());
    }

    /**
     * @depends testChangeStreamAffectsLabResults
     * @depends testGetActivityLogTableName
     */
    public function testActivityLogChange() {
        $ca = $this->certification_applications('0'); // from fixture, which is database since it resets it on load
        $pk = $ca->app_id;

        $old_value = $ca->certified_tx_spatial_stream_24;
        $this->assertEquals(2, $old_value);
        $new_value = 3;
        $ca->certified_tx_spatial_stream_24 = $new_value;
        $ca->save();

        $rows = $ca->getActivityLogRows();
        $this->assertInternalType('array', $rows, "should have been a returned array");
        $this->assertGreaterThan(0, count($rows), "should have returned rows for activity log");

        $row = $rows[0]; // sorted in DESC date, so this is last change
        $old_data = unserialize($row['old_data']);
        $new_data = unserialize($row['new_data']);

        // assure the activity log tracked the actual changed data
        $this->assertEquals($new_value, $new_data['certified_tx_spatial_stream_24']);
        $this->assertEquals($old_value, $old_data['certified_tx_spatial_stream_24']);

        // make sure we have app_id in both the new and old data array
        $this->assertEquals($pk, $old_data['app_id'], "could not find the app_id in the old_data array");
        $this->assertEquals($pk, $new_data['app_id'], "could not find the app_id in the new_data array");

        // assure we have certifications in both the new and old data array
        $this->assertNotNull($old_data['certifications'], "could not find certifications in the old_data array");
        $this->assertNotNull($new_data['certifications'], "could not find certifications in the new_data array");


    }

}
?>
