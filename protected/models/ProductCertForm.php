<?php

/**
 * Description of ProductCertForm
 *
 * @author Owner
 */
class ProductCertForm extends CFormModel {
    //put your code here

    public function __construct($scenerio='', $product_id){
        parent::__construct($scenerio);
        $this->loadProductId($product_id);
    }

    /**
     * set initial property values
     * @param array $in_hash
     */
    public function loadProductId($prod_id){

        
        // do both product model and certificationApplication model look ups
        $prod = Products::model()->findByPk($prod_id);

        // populate product values

        $ca_list = CertificationApplications::model()->find('product_id=:product_id', array('product_id'=>$prod_id));
        if (is_array($ca_list)){

        }
        else {
            if (!($ca_list instanceof CertificationApplications)){
                throw new Exception("database integrity issue, no existing application for product");
            }
        }
    }
}
?>
