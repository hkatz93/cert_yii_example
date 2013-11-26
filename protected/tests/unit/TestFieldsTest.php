<?php

/**
 * @covers TestFields
 */
class TestFieldsTest extends CDbTestCase {

    public $fixtures=array(
    //'requested_certifications'=>'RequestedCertifications',
    //'test_data'=>'TestData',
    //'certification_applications'=>'CertificationApplications'
    );

    /**
     * @covers TestFields::getConstantFieldIds
     */
    public function testConstantsExistInDatabase(){

        $ary = TestFields::getConstantFieldIds();
        $this->assertInternalType('array', $ary);
        foreach ($ary as $field_id){
            $tf = TestFields::model()->findByPk($field_id);
            $this->assertTrue($tf instanceof TestFields, "unable to find field_id=$field_id in TestFields");
        }
    }
}
?>
