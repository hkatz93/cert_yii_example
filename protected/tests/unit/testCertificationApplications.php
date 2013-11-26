<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//Yii::import('application.controllers.CertificationApplicationsController');
//Yii::import('application.models.RequestedCertifications');
//Yii::import('application.models.CertificationApplications');

/**
 * @covers CertificationApplications
 */
class CertificationApplicationsTest extends CDbTestCase {

    //public $fixtures=array(
    //'posts'=>'Post',
    //'comments'=>'Comment',
    //);

  //public function testAddRequestedCertification() {
  //    $ca_ctl = new CertificationApplicationsController;
  //    $rc = new RequestedCertifications;
  //    $rc->cert_id = 41;
  //    //$this->assertTrue($this->)
  //}
  public function testCRUD() {
    // create a new application
    $ca = new CertificationApplications();
    $tmp = array();
    $tmp['product_id'] = '20000';
    $ca->setAttributes($tmp);
    $this->assertTrue($ca->save(false));

    //READ back the newly created project
    $retrievedCa=CertificationApplications::model()->findByPk($ca->app_id);
    $this->assertTrue($retrievedCa instanceof CertificationApplications);
    $this->assertEquals($tmp['product_id'],$retrievedCa->product_id);

    //UPDATE the newly created project
    $company_contact = 'hkatz93@yahoo.com';
    $ca->company_contact = $company_contact;
    $this->assertTrue($ca->save(false));

    //read back the record again to ensure the update worked
    $retrievedCa=CertificationApplications::model()->findByPk($ca->app_id);
    $this->assertTrue($retrievedCa instanceof CertificationApplications);
    $this->assertEquals($company_contact,$retrievedCa->company_contact);

    //DELETE the project
    $ca_app_id = $ca->app_id;
    $this->assertTrue($ca->delete());
    $deletedCa=CertificationApplications::model()->findByPk($ca_app_id);
    $this->assertEquals(NULL,$deletedCa);
  }
}
?>
