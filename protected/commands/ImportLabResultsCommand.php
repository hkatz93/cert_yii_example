<?php
/**
 * enable parsing of CSV files
 */
require_once(dirname(__FILE__).'/../../../../classes/TestResultParser.php');

class ImportLabResultsCommand extends CConsoleCommand {
    
    /**
     * temporarily in test mode, only usable for data for WPS1 and WPS2 at the momement
     * and still it needs to copy the file over too
     * 
     * @param type $source_app_id
     * @param type $target_product_id 
     * 
     * @todo copy over file as well as data
     */
    public function actionOverwrite($filename, $app_id, $cert_id) {
        // verify that both exist
        print "----- WARNING: this is for testing ONLY -----\n";
        
        print " app_id = $app_id \n";
        print " cert_id = $cert_id\n";
        // validate file exists and is readable
        if (!file_exists($filename)){
            die("filename $filename does not exist");
        }
        
        if (!is_readable($filename)){
            die("filename $filename exists, but is not readable");
        }
        
        // validate app_id exists
        $app = CertificationApplications::model()->findByPk($app_id);
        if (!($app instanceof CertificationApplications)) {
            die("app_id ($source_app_id) does not exist");
            //throw new InvalidArgumentException("app_id ($source_app_id) does not exist");
        }
       
        // validate cert_id exists
        $cert = Certifications::model()->findByPk($cert_id);
        if (!($cert instanceof Certifications)) {
            die("cert_id ($cert_id) does not exist");
            //throw new InvalidArgumentException("target_product_id ($target_product_id) does not exist");
        }
        
        // perform the import
      
        // first delete all existing data
        $rc = RequestedCertifications::model()->find('app_id=:app_id AND cert_id=:cert_id', array(':app_id'=>$app_id, ':cert_id'=>$cert_id));
        
        $td_list = $rc->test_data;
        foreach ($td_list as $td){
            $rv = $td->delete();
            if (!$rv){
                print "unable to delete test_data (data_id={$td->data_id})";
            }
        }
        
        // insert all new data
        // load data from external excel file
        $rp = new TestResultParser();
        $rp->setCertId($cert_id);
        
        // get product type
        $prod = $app->products;
        print "product_id = {$prod->product_id}, ";
        if ($prod->isAccessPoint()){
            print "product type = AP, ";
            $rp->setTestType(TestResultParser::TEST_TYPE_ACCESS_POINT);
        }
        else {
            print "product type = STA, ";
            $rp->setTestType(TestResultParser::TEST_TYPE_STATION);
        }
        print "request_id = {$rc->request_id}, ";
        
     
        $extracted_ary = $rp->extractFieldData($filename);
        print "\n\nextracted_ary = \n";
        print_r($extracted_ary);
        
        // insert the records
        foreach ($extracted_ary as $key => $value){
            // look up the field id
            $test_type = $rp->getTestType();
            //print "looking up test_fields: (cert_id = $cert_id, test_type=$test_type, field_name=$key)\n";
            $tf = TestFields::model()->find('cert_id=:cert_id AND test_type=:test_type AND field_name=:field_name',
                    array(':cert_id'=>$cert_id, ':test_type'=>$test_type, ':field_name'=>$key));
            if (!($tf instanceof TestFields)){
                print "unable to lookup test_fields (cert_id = $cert_id, test_type=$test_type, field_name=$key) \n";
            }
            else {
                //print "the test_field object";
                // insert the data
                unset($td);
                $td = new TestData();
                $td->request_id = $rc->request_id;
                $td->field_id = $tf->field_id;
                $td->data = $value;
                $td->posted_on = date('Y-m-d H:i:s');
                $td->posted_by =7791; // hkatz@wi-fi.org
                $rv = $td->save();
                if (!$rv){
                    $errors = $td->getErrors();
                    print "unable to set data: (field_id={$tr->field_id}): ";
                    print_r($errors);
                    print "\n";
                }
                else {
                  print "updated  {$tf->field_name} = {$td->data}\n";
                }
            }                
        }
    }
    
    
    public function actionIndex() {
        print "Usages: \n";
        print "\t./yiic importlabresults overwrite --file=filename --app_id=value --cert_id=value \n";
        
    }
    
}