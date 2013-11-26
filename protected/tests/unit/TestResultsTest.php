<?php

/**
 * @covers TestResults
 */
class TestResultsTest extends CDbTestCase {

    public $fixtures=array(
    //'products'=>'Products',
    //'certification_applications'=>'CertificationApplications',
    //'product_certifications'=>'ProductCertifications',
    'requested_certifications'=>'RequestedCertifications',
    'test_results'=>'TestResults'
    );

    public function testRelationsRequestedCertifications() {

        $tr = TestResults::model()->findByPk(1); // from database
        $tr= $this->test_results('0'); // from fixture, which is database since it resets it on load
        //$cert = Certifications::model()->with('requested_certifications')->findByPk(41);
        $this->assertTrue($tr instanceof TestResults);

        // lazy load
        $rc = $tr->requested_certifications;
        //$this->assertTrue(is_array($apps), "is not an array");
        $this->assertTrue($rc instanceof RequestedCertifications, "is not instance of RequestedCertifications");

    }

    public function testDelete() {
        $tr = $this->test_results('0');
        $request_id = $tr->request_id;
        $rv = $tr->delete();
        $this->assertFalse($rv == true, "record delete should have been prevented");

        // try deleting the "correct" way by deleting the requested certification first
        //$rc = $tr->requested_certifications;
        $rc = RequestedCertifications::model()->findByPk($request_id);
        //$rc_id = $rc->request_id;
        //print "rc_id= ".$rc->request_id."\n";
        $rv = $rc->delete();
        $this->assertTrue($rv == true, "record deletion of requested_certifications should have been allowed");
        $rc2 = RequestedCertifications::model()->findByPk($request_id);
        //print "rc2_id= ".$rc2->request_id."\n";
        //$rc2 = RequestedCertifications::model()->findByPk(999);
        $this->assertFalse(($request_id == $rc2->request_id), "delete should have prevented loading again");

        // Oddly, this still gives results from the deleted record
        // Perhaps something about fixtures?
        // 
        //$rc3 = $tr->requested_certifications;
        //print "rc3->request_id = ".$rc3->request_id."\n";
        //print_r($rc);

        $rv = $tr->delete();
        $this->assertFalse($rv == true, "record deletion of test_result should not have been allowed");
    }

    function testAddTestResult() {
        // make sure validation is working properly
	$tr = $this->test_results('0');
        // grab a working request_id
        $rc = $this->requested_certifications('0'); // from fixture
        $this->assertTrue($rc instanceof RequestedCertifications);
        $this->assertTrue(!empty($rc->request_id), "request_id should not have been empty");
	
        $tr = TestResults::model()->findByPk(1); // from database
        $tr= $this->test_results('0'); // from fixture, which is database since it resets it on load
        $this->assertTrue($tr instanceof TestResults);
		
        // try to insert with a bad request_id
        $tmp = array();
        $tmp['request_id'] = 99999;
        $tmp['result'] = TestResults::PASS;
        $tr = new TestResults();
        $tr->setAttributes($tmp);
        $this->assertFalse($tr->save(), "validation should have prevented saving with bad request_id");

        // try to insert with a bad result
        $tmp = array();
        $tmp['request_id'] = $rc->request_id;
        $tmp['result'] = 99999;
        $tr = new TestResults();
        $tr->setAttributes($tmp);
        $this->assertFalse($tr->save(), "validation should have prevented saving with bad result");

	// try to insert with a bad  import_result_id values
        $tmp = array();
	$tmp['request_id'] = $rc->request_id;		
        $tmp['import_result_id'] = 12345679; // bad result id
        $tmp['result'] = TestResults::NOT_TESTED;
        $tr = new TestResults();
        $tr->setAttributes($tmp);
        $this->assertFalse($tr->save(), "validation should have prevent saving with bad  test import_result_id");
		
		
	// try to insert with a good  import_result_id values
        $tmp = array();
	$tmp['request_id'] = $rc->request_id;		
        $tmp['import_result_id'] = $tr->result_id;
        $tmp['result'] = TestResults::NOT_TESTED;
        $tr = new TestResults();
        $tr->setAttributes($tmp);
        $this->assertTrue($tr->save(), "validation should have prevented saving with bad test result id");
		
        // try to insert with good values
        $tmp = array();
        $tmp['request_id'] = $rc->request_id;
        $tmp['result'] = TestResults::NOT_TESTED;
        $tr = new TestResults();
        $tr->setAttributes($tmp);
        $this->assertTrue($tr->save(), "validation should not have prevented saving with good results");

        // check to see if the posted_on date is populated
        $tr2 = TestResults::model()->findByPk($tr->result_id); // will not be updated until reloaded
        $this->assertTrue($tr2 instanceof TestResults, "not an instance of TestResults");
        $date1 = strtotime($tr2->posted_on);
        $epoch_date = strtotime("0000-00-00 00:00:00");
        $this->assertTrue($date1 > $epoch_date, "posted_on should have been populated with current date");
		
	
    }

    public function testResultsDropDownArray(){
        $ary = TestResults::resultsDropDownArray();
        $this->assertInternalType('array', $ary);
        $this->assertEquals('Pass', $ary[TestResults::PASS]);
        $this->assertEquals('Fail', $ary[TestResults::FAIL]);
        $this->assertEquals('Not Tested', $ary[TestResults::NOT_TESTED]);
    }
	
}
?>
