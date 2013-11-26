<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * @todo delete this test and related forms
 */

/**
 * Description of ProductCertFormTest
 *
 * @author Owner
 */
class ProductCertFormTest extends CDbTestCase{

     public $fixtures=array(
    //'products'=>'Products',
    //'certification_applications'=>'CertificationApplications',
    //'product_certifications'=>'ProductCertifications',
    //'requested_certifications'=>'RequestedCertifications',
    //'test_data'=>'TestData',
    );

    public function testConstruct() {
        //$prod = $this->products('0');
        $prod = Products::model()->findByPk(1);
        $prod_id = $prod->product_id;
        
        $pcf = new ProductCertForm('', 1);
        $this->assertTrue($pcf instanceof ProductCertForm);
    }
}
?>
