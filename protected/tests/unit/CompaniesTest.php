<?php

/**
 * @covers Companies
 */
class CompaniesTest extends CDbTestCase {

    public $fixtures=array(
    //'requested_certifications'=>'RequestedCertifications',
    //'comments'=>'Comment',
    );


    public function testScopeActive() {
        $co = Companies::model()->active()->findAll();
        $this->assertTrue(is_array($co), " should be an array");
        //print_r($co[0]);
        $this->assertTrue($co[0] instanceof Companies, " \$co[0] should be an instanceof Companies");
    }

    public function testScopeInactive() {
        $co = Companies::model()->inactive()->findAll();
        $this->assertTrue(is_array($co), " should be an array");
        $this->assertTrue($co[0] instanceof Companies, " should be an instanceof Companies");
    }

    /**
     * @depends testScopeActive
     * @depends testScopeInactive
     */
    public function testActiveAndInactiveScopeExclusive() {
        // grab one active company_id for later comparison
        $co = Companies::model()->active()->findAll();
        $active_company_id = $co[0]->company_id;
        $this->assertTrue(is_numeric($active_company_id), "company id should be numeric");

        // check to see that we cannot find an active company in the inactive list
        $co = Companies::model()->inactive()->find('company_id='.$active_company_id);
        $this->assertFalse($co instanceof Companies, " should be blank and not an instanceof Companies");
      
        // check to see that we can find an active company in the active list
        $co2 = Companies::model()->active()->find('company_id='.$active_company_id);
        $this->assertTrue($co2 instanceof Companies, " should be an instanceof Companies");

        // now lets reverse the comparison
        // --------------------------------------
        // grab one inactive company_id for later comparison
        $co = Companies::model()->inactive()->findAll();
        $inactive_company_id = $co[0]->company_id;
        $this->assertTrue(is_numeric($inactive_company_id), "company id should be numeric");

        // check to see that we cannot find an inactive company in the active list
        $co = Companies::model()->active()->find('company_id='.$inactive_company_id);
        $this->assertFalse($co instanceof Companies, " should be blank and not an instanceof Companies");

        // check to see that we can find an inactive company in the inactive list
        $co2 = Companies::model()->inactive()->find('company_id='.$inactive_company_id);
        $this->assertTrue($co2 instanceof Companies, " should be an instanceof Companies");
    }

    /**
     * make sure that WFA is an active company
     * @depends testScopeActive
     */
    public function testWfaIsActive() {
        // check to see that we can find an active company in the active list
        // the following code does not insert the placeholder (BUG w/ yii?)
        //$co = Companies::model()->active()->find('company_id=:company_id', array(':company_id'=>Companies::COMPANY_ID_WFA));

        $co = Companies::model()->active()->find('company_id='.Companies::COMPANY_ID_WFA);
        $this->assertTrue($co instanceof Companies, " should be an instanceof Companies");
    }

    /**
     * @depends testScopeInactive
     * @depends testScopeActive
     * @depends testActiveAndInactiveScopeExclusive
     */
    public function testScopeActivePlusCurrent(){
        // grab an inactive company
        $co = Companies::model()->inactive()->find();
        $co_list = $co->active_plus_current()->findAll();
        $this->assertInternalType('array', $co_list);
        $this->assertTrue($co_list[0] instanceof Companies);

        $co_active_list = Companies::model()->active()->findAll();
        $this->assertEquals(count($co_list), count($co_active_list)+1, "active_plus_current scope should have one more than active scope");

        // assure that the active_plus_current scope works when current is active company as well
        $co = Companies::model()->active()->find();
        $co_list = $co->active_plus_current()->findAll();
        $this->assertInternalType('array', $co_list);
        $this->assertTrue($co_list[0] instanceof Companies);

        $co_active_list = Companies::model()->active()->findAll();
        $this->assertEquals(count($co_list), count($co_active_list), "active_plus_current scope should have same as active scope when current company is active");

        // assure that the active_plus_current scope works when current does not exist
        $co_list = Companies::model()->active_plus_current()->findAll();
        $this->assertInternalType('array', $co_list);
        $this->assertTrue($co_list[0] instanceof Companies);

        $co_active_list = Companies::model()->active()->findAll();
        $this->assertEquals(count($co_list), count($co_active_list), "active_plus_current scope should have same as active scope when current company does not exist");

    }
    // these relations are coding that is logical but probably not useful, ignore for now
    //public function testRelations() {

        //$co = Companies::model()->findByPk(Companies::COMPANY_ID_WFA);

        // lazy load
        //$u = $cert->users;
        //$this->assertTrue(is_array($u), " is not an array");
        //$this->assertTrue($u[0] instanceof Users, "is not instance of Users");

        // lazy load
        //$p = $cert->products;
        //$this->assertTrue(is_array($p), " is not an array");
        //$this->assertTrue($p[0] instanceof Products, "is not instance of Products");
    //}
}
?>
