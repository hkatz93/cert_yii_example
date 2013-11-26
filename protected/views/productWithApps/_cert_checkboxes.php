<?php
/* 
 * used to display the certification checkboxes used in products and applications
 *
 * $is_test_engine (integer) 1 || 0
 *
 * certificationsArray is passed in
 * 
 *
 * $cert_id_list (array) is passed in
 *    example array element - array('id'=>2, 'text'=>'802.11a, 'group'=>'Standard IEEE');
 *
 * $cert_lab_data (array) can be passed in
 *    example array(2 => 13423); // key = cert_id, value= request_id of results
 *
 * $field_prefix (string) is passed in (ex- "Products" or "CertificationApplications[1]")
 * $table_class (string) product_cert_edit | app_cert_edit
 */
if ($is_test_engine == 1){
    // test engine list of certifications
    $cert_ary = Certifications::getDropDownArray(array('is_test_engine'=>1));
}
elseif (isset($is_test_engine) && $is_test_engine == 0) {
    // non-test engine list of certifications
    $cert_ary = Certifications::getDropDownArray(array('is_test_engine'=>0));
    
}
else {
    $cert_ary = Certifications::getDropDownArray(); // contains both test_engine and without
}

// calculate if has lab results
$rows = $certificationsArray;

foreach ($rows as $row) {
    // calculate whether has test data
    $td = TestData::model()->find('request_id=:request_id', array('request_id'=>$row['request_id']));
    $has_test_data = (!empty($td->data_id)) ? true : false;
    if ($has_test_data){
        $cert_lab_data_request_id["{$row['cert_id']}"] = $row['request_id'];
    }
}


$table_row_ary = array();
//$row_span = 0;
$tr_index = 0;
$prev_category ='';
foreach ($cert_ary as $index=>$row){
    
    // determine if the row is at the start of the group
    if ($index !=0 && $prev_category != $row['group']){
        $prev_category = $row['group'];
        $tr_index++;
    }
    elseif ($index == 0) {
        $prev_category = $row['group'];
    }
    $tr = &$table_row_ary[$tr_index];
    $tr['category'] = $row['group'];
    
    $tmp_ary = array();
    $tmp_ary['cert_id'] = $row['id'];
    $tmp_ary['display_name'] = $row['text'];
    $tmp_ary['checked'] = (in_array($row['id'], $cert_id_list)) ? 1 : 0;
    
    $tr['cert_list'][] = $tmp_ary;    
}

// create html for rows
$html = "<table class='$table_class'>\n";
foreach ($table_row_ary as $tr_i => $ary) {
    $html .= "<tr class='category_row'>";
    $row_span = count($ary['cert_list']);
    // first column
    $html .= "<td rowspan='$row_span' class='category_column'>{$ary['category']}</td>\n";

    // second column
     $first_row = true;
    // certification checkboxes
    foreach ($ary['cert_list'] as $key => $cert_row) {
        if (!$first_row) {
            $html .= "<tr>";
        }
        else {
            $first_row = false;
        }
        $html .=  "<td>";
        $checked = ($cert_row['checked']) ? "checked='checked'" : '';
        $html .= "<input type='checkbox' name='{$field_prefix}[cert_id_list][]' value='{$cert_row['cert_id']}' $checked /> {$cert_row['display_name']} ";
        if (isset($cert_lab_data_request_id["{$cert_row['cert_id']}"])){
            //$html .= ' Edit Data';
            $html.= CHtml::link('Edit Data', array('productLabResults/index', 'id'=>$cert_lab_data_request_id["{$cert_row['cert_id']}"]));
        }
        $html .= "</td>";
        $html .= "</tr>\n";
    }
    $html .= "</tr>\n";
}
$html .= "</table>\n";
print $html;
//print "<pre>"; print_r($table_row_ary); print "</pre>";
?>
