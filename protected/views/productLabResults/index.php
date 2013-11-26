<?php
// lab result form
// Get client script
$cs=Yii::app()->clientScript;
// Add CSS
$cs->registerCSSFile('/admin/css/mytables.css'); // consider a global inclusion


$js = <<<EOD
$('div.data_edit a').click(
    function (){
        var table_row = $(this).parent().parent().parent();
        //alert("clicked");
        $(this).hide();
        $(this).parent().children('div.form_elements').show();
        //table_row.css('background-color', '#DEBD52'); // table row
        table_row.css('border', '2px dotted #DEBD52');
        //table_row.css('border-size', '1');
        table_row.children('td').css('font-weight', 'bold'); 
    }
);
EOD;

$js2 = <<<EOD
$('.data_setter').change(
    function (){
        //alert("value changed");
        var table_row = $(this).parent().parent().parent().parent();
        table_row.css('background-color', '#DEBD52');
        table_row.css('border', 'none');
        $(this).parent().children('.changed_data').val($(this).val());
    }
);
EOD;

Yii::app()->clientScript->registerScript('showEdit', $js, CClientScript::POS_READY);
Yii::app()->clientScript->registerScript('highlightEdit', $js2, CClientScript::POS_READY);


$this->breadcrumbs=array(
	'Product Lab Results',
);

$id = $_GET['id'];
//$id = 125477;
$id = (preg_match("/\d+/", $id)) ? $id : 0;
//$sql = "SELECT tf.*, td.*
/*
 * dataProviders are generally used for pagination
 * CDataList was not convienient to use since it required an attribute list
 * that did not seem to allow numberical arrays
 * 
 * $sql = "SELECT tf.field_name, td.data
 *    FROM certification_applications as ca
 *    INNER JOIN requested_certifications as rc ON (rc.app_id = ca.app_id)
 *    INNER JOIN test_data as td ON (td.request_id = rc.request_id)
 *    INNER JOIN test_fields as tf ON (tf.field_id = td.field_id)
 *    WHERE rc.request_id=$id
 *    ORDER BY tf.placement";
 *$rawData=Yii::app()->db->createCommand("SELECT * FROM test_data LIMIT 10")->queryAll();
 *  or using: $rawData=User::model()->findAll();
 * $dataProvider=new CArrayDataProvider($rawData, array(
 *    'id'=>'product_test_results',
 *    //'sort'=>array(
 *    //    'attributes'=>array(
 *    //         'id', 'username', 'email',
 *    //    ),
 *    //),
 *    //'pagination'=>array(
 *    //    'pageSize'=>10,
 *    //),
 *));
 */
// $dataProvider->getData() will return a list of arrays.

//$td_ary = TestData::model()->with('test_fields')->findAll('request_id=:request_id',array('request_id'=>$id));
$td_ary = TestData::model()
    ->with('test_fields')
    ->findAll(array(
        'order'=>'placement',
        'condition'=>'request_id=:request_id',
        'params'=>array('request_id'=>$id)));

$rc = RequestedCertifications::model()->findByPk($id);
$app = $rc->certification_applications;
$product = $app->products;
?>

<div class="form">
<?php
    if (Yii::app()->getUser()->hasFlash('success')) {
        print '<div class="notice_box">';
        print "<p>".Yii::app()->user->getFlash('success')."</p>";
        if (Yii::app()->getUser()->hasFlash('notice_list')) {
            $ary = Yii::app()->user->getFlash('notice_list');

            print "<ul>";

            foreach ($ary as $key => $value) {
                print "<li>$value</li>";
            }
            print "</ul>";
        }
        print "</div>";
    }
?>



<?php
    if (Yii::app()->getUser()->hasFlash('failure')) {
        print '<div class="errorSummary">';
        print "<p>".Yii::app()->user->getFlash('failure')."</p>";
        if (Yii::app()->getUser()->hasFlash('error_list')) {
            $ary = Yii::app()->user->getFlash('error_list');
            print "<ul>";
            foreach ($ary as $key => $value) {
                print "<li>$value</li>";
            }
            print "</ul>";
        }
        print '</div>';
    }
?>


<h2><?php echo $product->cid; ?> Test Results for <?php echo $rc->certifications->display_name; ?></h2>

<form  action="<?php echo CHtml::normalizeUrl(array('productLabResults/update', 'id'=>$id)); ?>" method="post">
    <?php echo CHtml::hiddenField('id',$id); ?>
<table id="dv1" class="mydetail-view">
    <tr>
    <th style="text-align: left">field name</th>
    <th style="text-align: left">data</th>
    </tr>

<?php
$i = 0;
foreach ($td_ary as $td) {
    // calculate the form element
    if (preg_match('|/|', $td->test_fields->format)){
        $valid_values = explode('/', $td->test_fields->format);
        $form_elements = CHtml::dropDownList("input_test_data[{$td->data_id}]", $td->data, array_combine($valid_values, $valid_values), array('class'=>'data_setter'));
    }
    else {
        $form_elements = CHtml::textField("input_test_data[{$td->data_id}]", $td->data, array('class'=>'data_setter', 'size'=>'10'));
    }
    $form_elements .= CHtml::hiddenField("TestData[{$td->data_id}]", '', array('class'=>'changed_data'));
    //$form_elements .= CHtml::hiddenField("TestData[{$td->data_id}]", $td->data, array('class'=>'orig_data'));

    echo "<tr class='".(($i % 2) ?'even':'odd')."'>\n";
    echo "<td width='65%'>".$td->test_fields->field_name."</td>\n";
    echo "<td>".$td->data."</td>\n";
    echo "<td><div class='data_edit'><a class='edit_link' style='cursor:pointer'>Edit</a><div style='display: none' class='form_elements'>$form_elements</div></div></td>\n";
    echo "</tr>\n";
    $i++;
}
?>
</table>
    <p style="margin: 20px 0 0 0 ">
    <?php
    echo CHtml::submitButton('Save Changes', array('confirm'=>'Save these changes?'));
    echo "&nbsp;";
    echo CHtml::button('Back to Product', array('submit'=>array('productWithApps/view', 'id'=>$app->product_id
    //echo CHtml::button('Cancel', array('submit'=>array('products/view', 'id'=>$product->product_id
        )));
    ?>
        </p>
</form>

<?php
//$this->widget('zii.widgets.CListView', array(
//	'dataProvider'=>$dataProvider,
//	'itemView'=>'_view',
//));
?>

</div>