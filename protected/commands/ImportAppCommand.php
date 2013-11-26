<?php
/**
 * enable parsing of CSV files
 */
require_once(dirname(__FILE__).'/../../../../classes/parsecsv/parsecsv.lib.php');

class ImportAppCommand extends CConsoleCommand {
    
    /**
     * temporarily in test mode
     * 
     * @param type $source_app_id
     * @param type $target_product_id 
     */
    public function actionInherit($source_app_id, $target_product_id) {
        // verify that both exist
        print "----- WARNING: this is for testing ONLY -----\n";
        
        print " source_app_id = $source_app_id \n";
        print " target_product_id = $target_product_id\n";
        
        // validate app_id exists
        $parent_app = CertificationApplications::model()->findByPk($source_app_id);
        if (!($parent_app instanceof CertificationApplications)) {
            die("app_id ($source_app_id) does not exist");
            //throw new InvalidArgumentException("app_id ($source_app_id) does not exist");
        }
        
        
        // validate product_id exists
        $dep_prod = Products::model()->findByPk($target_product_id);
        if (!($dep_prod instanceof Products)) {
            die("target_product_id ($target_product_id) does not exist");
            //throw new InvalidArgumentException("target_product_id ($target_product_id) does not exist");
        }
        
        // perform the import
        
        // copy the external data
        
        
        // TEST CODE ONLY
        // create a fake dependency
        //
        
        $ca_list = $dep_prod->certification_applications;
        
        $ca = (is_array($ca_list)) ? $ca_list[0] : $ca_list;
        // make sure the dependent product app is publishable
        $ca->hold = 0;
        $ca->publish_on ='Deferred Date';
        $ca->status = CertificationApplications::STATUS_COMPLETE;
        $ca->deferred_date = '2000-01-01'; // before today
        
        if (!$ca->save()) {
            die("trouble saving dep app");
        }
        else {
            print "saved cert application...\n";
        }
        
        /*
        $dep_prod->parent_id = $parent_app->product_id;
        if (!$dep_prod->save()) {
            die("trouble saving dep prod");
        }
        */
    }
    
    /**
     * pushes the changes from the parent to the child products
     * @param integer $app_id identifies the application whose certifications we will copy to the dependents
     */
    public function actionUpdateDependentProducts($app_id) {
        $parent_app = CertificationApplications::model()->findByPk($app_id);
        if (!($parent_app instanceof CertificationApplications)){
            throw new InvalidArgumentException('app_id does not retrieve a valid CertificationApplications object');     
        }
        $parent_prod = Products::model()->findByPk($parent_app->product_id);
        print "updating the following dependent products: (product_id)\n";
        print "========================================================\n";
        $dep_prod_list = $parent_prod->getDependentProducts();
        if (count($dep_prod_list) == 0) {
            print "-- [No dependent products were found for product_id={$parent_prod->product_id}] --\n\n";
        }
        
        $i = 1;
        foreach ($dep_prod_list as $dep_prod) {
            print $i++;
            print ": " .$dep_prod->product_id; print "\n";
            
            print "\t applying import...\n";
            $rv = $dep_prod->appendParentApplicationResults($app_id);
            if (!$rv){
                $errors = $dep_prod->errors;
                print "\t errors = ";
                print_r($errors);
            }
            
        }
        
        // this will be a test mostly
        // we will create a temporary command to process case 4730 specifically
        // which will grab the dependents from a hardcoded list which is a subset
        // of all the dependents
        
        // for now lets just have at it and apply the import to all dependents
        // Yee Haw!
        
        
        
        
    }
    
    public function actionUpdateDependents($file){
    
        $rows = $this->_parseDepFile($file);
        //print_r($rows);
        
        print "-- update dependent products --\n\n";
        foreach ($rows as $row) {
            print "-> updating {$row['product_id']} with app_id={$row['parent_app_id']} <-\n";
            // import parent_app to dependent product
            $dep_prod = Products::model()->findByPk($row['product_id']);
            if (!($dep_prod instanceof Products)){
                print "ERROR: not able to load product\n\n";
            }
            $rv = $dep_prod->appendParentApplicationResults($row['parent_app_id']);
            if (!$rv){
                $errors = $dep_prod->errors;
                print "\t errors = ";
                print_r($errors);
                
            }
            $dep_prod->product_notes = $row['product_notes'];
            $dep_prod->firmware = $row['firmware'];
            $rv = $dep_prod->save();
            if (!$rv) {
                print "-- unable to save() to dependent product\n";
            }
        }
    }
    
    /**
     * @param string $file full path of file to parse
     * @param integer $limit if not null then restrict output to this number of lines
     * @return array $rows of data
     */
    private function _parseDepFile($file, $limit=null){
        $required_flds = array('cid', 'parent_app_id');
        
        if (!file_exists($file)){
            die('cannot locate file:' . $file ."\n");
        }
        if (!is_readable($file)) {
            die('file is not readable:' . $file."\n");
        }
        
        // create new parseCSV object.
        $csv = new parseCSV();

        // limit the number of returned rows.
        if (isset($limit)){
            $csv->limit = $limit;
        }

        // Parse $file using automatic delimiter detection.
        $csv->auto($file);

        // Output result.
        $rows = $csv->data;
        
        // check required fields to see if the columns exist
        $col_flds = array_keys($rows[0]);
        $missing_col_flds = array_diff($required_flds, $col_flds);
        if (count($missing_col_flds)){
            throw new Exception('the following required columns are missing from CSV file:'.(implode(',',$missing_col_flds)));
            //die('the following required columns are missing from CSV file:'.(implode(',',$missing_col_flds))."\n");
        }
        
        
        // clean data. trim out leading and trailing white spaces
        foreach ($rows as $index => $row){
            foreach ($row as $key=>$value){
                $value = trim($value);
                if ($key == 'product_notes'){
                    //$value = strip_tags($value);
                    //$value = nl2br($value);
                }
                if ($key == 'cid') {
                    $rows[$index]['product_id'] = Products::cid2ProductId($value);
                }
                if ($key == 'parent_cid') {
                    $rows[$index]['parent_product_id'] = Products::cid2ProductId($value);
                }
                // validate that the required field is not blank
                if (in_array($key, $required_flds) && empty($value)){
                    throw new InvalidArgumentException("required field '$key' cannot be blank");
                }
                $rows[$index][$key] = $value;
            }
        }
        return $rows;
    }
    
    public function actionIndex() {
        print "Usages: \n";
        print "\t./yiic importapp inherit --source_app_id=value --target_product_id=value \n";
        print "\t./yiic importapp updatedependentproducts --app_id=value \n";
        print "\t./yiic importapp updatedependents --file=value \n";
    }
    
}