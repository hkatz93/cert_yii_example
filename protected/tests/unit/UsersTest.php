<?php

class UsersTest extends CDbTestCase {

    public $fixtures=array(
    'certification_applications'=>'CertificationApplications',
    //'product_certifications'=>'ProductCertifications',
    //'products'=>'Products',
    //'requested_certifications'=>'RequestedCertifications',
    //'test_data'=>'TestData',
    //'test_results'=>'TestResults',
    //'activity_log'=>'ActivityLog', // just blanked out
    );


    public $lab_username = '_wfa_lab@wi-fi.org';
    public $test_username = 'wfa_member@wi-fi.org';
    public $superadmin_username = 'dbaczewski@wi-fi.org';


    public function testUsersExist() {
        $user = Users::model()->find('username=:username', array('username'=>$this->lab_username));
        //$user = Users::model()->findByPk(7828); //wfa_lab@wi-fi.org
        $this->assertTrue($user instanceof Users, "user {$this->lab_username} not found");

        $user = Users::model()->find('username=:username', array('username'=>$this->superadmin_username));
        //$user = Users::model()->findByPk(6960); //dbaczewski@wi-fi.org
        $this->assertTrue($user instanceof Users, "user {$this->superadmin_username} not found");

        $user = Users::model()->find('username=:username', array('username'=>$this->test_username));
        //$user = Users::model()->findByPk(7829); //wfa_member@wi-fi.org
        $this->assertTrue($user instanceof Users, "user {$this->test_username} not found");
    }

    /**
     * @depends testUsersExist
     */
    public function testIsSuperAdmin(){
        $user = Users::model()->find('username=:username', array('username'=>$this->superadmin_username));
        $this->assertTrue($user->isSuperAdmin(), "user should have been a super admin (username = {$this->superadmin_username})");

        $user = Users::model()->find('username=:username', array('username'=>$this->test_username));
        $this->assertFalse($user->isSuperAdmin(), "user should NOT have been a super admin (username = {$this->test_username})");
    }

    /**
     * @depends testUsersExist
     */
    public function testRelationsLabs() {
        $user = Users::model()->find('username=:username', array('username'=>$this->lab_username));
        $labs = $user->labs;
        $this->assertTrue($labs instanceof Labs, "should have found a lab for this user: {$this->lab_username}");
    }


    /**
     * @depends testUsersExist
     */
    public function testRelationsCertificationApplications() {
        $ca = $this->certification_applications('0');
        $this->assertTrue($ca instanceof CertificationApplications, "should have found CertificationApplication");
        //$user = Users::model()->find('username=:username', array('username'=>$this->test_username));
        $user = Users::model()->findByPk($ca->requested_by);
        $this->assertTrue($user instanceof Users, "should have found user");
        $apps = $user->certification_applications;
        $this->assertInternalType('array', $apps);
        //print_r($apps);
        $this->assertTrue($apps[0] instanceof CertificationApplications, "should have found app for this user: {$this->test_username}");
    }

    /**
     * @depends testUsersExist
     */
    public function testRelationsCompanies() {
        $user = Users::model()->find('username=:username', array('username'=>$this->test_username));
        $co = $user->companies;
        $this->assertTrue($co instanceof Companies, "should have found a company for this user: {$this->test_username}");
    }

    
}
?>
