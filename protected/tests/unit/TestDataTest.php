<?php

/**
 * @covers TestData
 */
class TestDataTest extends CDbTestCase {

    public $fixtures=array(
    //'requested_certifications'=>'RequestedCertifications',
    'test_data'=>'TestData',
    'certification_applications'=>'CertificationApplications'
    );

    
    public function testRelationsTestFields(){


        $td = $this->test_data('0'); // from fixture, which is same as db since db is reset on unit test load
        $this->assertTrue($td instanceof TestData, "should be an instance of TestData");

        // Test for test_fields relation
        $tf = $td->test_fields;
        $this->assertTrue($tf instanceof TestFields, "should be an instance of TestFields");

    }


    public function testRelationsRequestedCertifications() {
        $td = $this->test_data('0');
        $rc = $td->requested_certifications;
        $this->assertTrue($rc instanceof RequestedCertifications, "should have been an instance of RequestedCertifications");
    }


    public function testPreventBadData() {
        //$tf = TestFields::model()->find('format=:format', array('format'=>'Pass/Fail'));
        $td = TestData::model()->with('test_fields')->find('format=:format', array('format'=>'Pass/Fail'));
        $this->assertTrue($td instanceof TestData);
        $this->assertEquals('Pass/Fail', $td->test_fields->format);

        $td->data = '0';
        $this->assertFalse($td->save(), "validation should have prevented saving data");

        $td->data = 'Pass';
        $this->assertTrue($td->save(), "validation should not not have prevented saving data");

        //$errors = $td->getErrors();        print " -- errors = "; print_r($errors);

        $td = TestData::model()->with('test_fields')->find('format=:format', array('format'=>'0/1/2/3'));
        $this->assertTrue($td instanceof TestData);
        $this->assertEquals('0/1/2/3', $td->test_fields->format);

        $td->data = 'A';
        $this->assertFalse($td->save(), "validation should have prevented saving data");

        $td->data = '3';
        $this->assertTrue($td->save(), "validation should not not have prevented saving data");

        $td = TestData::model()->with('test_fields')->find('format=:format', array('format'=>'Text'));
        $td->data = '';
        $this->assertFalse($td->save(), "validation should have prevented saving data");
        
    }

    public function testUnrestrictedTextData() {
        $td = TestData::model()->with('test_fields')->find('format=:format', array('format'=>'Text'));
        $this->assertTrue($td instanceof TestData);
        $this->assertEquals('Text', $td->test_fields->format);

        $td->data = 'Pass';
        $this->assertTrue($td->save(), "validation should not not have prevented saving data");

        $td->data = 'Some Text Value';
        $this->assertTrue($td->save(), "validation should not not have prevented saving data");
    }

    public function testGetTestDataByAppIdAndFieldId() {
        $ca = $this->certification_applications('0');
        $td = TestData::model()->getTestDataByAppIdAndFieldId($ca->app_id, TestFields::AP_MAX_TX_STREAMS_24_CERT_N_APPROVED);
        
        $this->assertTrue($td instanceof TestData, "not instance of TestData, (not found?)");
        $this->assertEquals(7, $td->data_id);
        
        //print "data_id = ".$td->data_id;
    }
    public function testMaxInt() {
        $this->assertEquals(TestData::maxInt(10,3), 3);
        $this->assertEquals(TestData::maxInt(1,3), 1);
        $this->assertEquals(TestData::maxInt('A',3), 'A');
    }
}
?>
