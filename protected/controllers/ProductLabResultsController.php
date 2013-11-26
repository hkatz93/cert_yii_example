<?php

class ProductLabResultsController extends Controller
{
        /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
        /**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
            return array(
			//array('allow',  // allow authenticated users to perform 'index' and 'view' actions
			//	'actions'=>array('index','view'),
			//	'users'=>array('@'),
			//),
			//array('allow', // allow authenticated user to perform 'create' and 'update' actions
			//	'actions'=>array('create','update'),
			//	'users'=>array('@'),
			//),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('create','admin','delete','update', 'index', 'view'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
                /*
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('create', 'update', 'admin','delete','AutoCompleteLookupUsername','AddCertification', 'AutoCompleteLookupCompanyContact'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
                 *
                 */
	}

	public function actionEdit()
	{
		$this->render('edit');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

        public function actionUpdate(){
            if (isset($_POST['TestData'])){

                // grab all the active records for TestData
                $td_ary = array();
                foreach ($_POST['TestData'] as $data_id => $form_value) {
                    $v = trim($form_value);
                    if (strlen($v) > 0) { // only update fields that are not blank
                        $td = TestData::model()->findByPk($data_id);
                        $td->data = $v;
                        $td_ary[] = $td;
                    }
                }

                // check the validation for each of the updates
                $error_ary = array();
                foreach ($td_ary as $td){
                    if (!$td->validate()){
                        $error_ary["{$td->data_id}"] = $td->test_fields->field_name.': '.$td->getError('data');
                    }
                }

                // if validation is ok, run the updates
                if (count($error_ary) == 0) {
                    foreach ($td_ary as $td){
                        // load up old data
                        $td_old = TestData::model()->findByPk($td->data_id);
                        if (!$td->save()) {
                           $error_ary["{$td->data_id}"] = $td->test_fields->field_name.': '.$td->getError('data');
                        }
                        else {
                           $notice_ary["{$td->data_id}"] = $td->test_fields->field_name
                                   //.': updated from '.$_POST['TestData']["{$td->data_id}"].' to '.$td->data;
                                   .': updated from "'.$td_old->data.'" to "'.$td->data.'"';
                        }
                    }
                }

                // set the error messages if failures exist
                if (count($error_ary) > 0) {
                    Yii::app()->user->setFlash('failure', 'Update has failed due to errors');
                    Yii::app()->user->setFlash('error_list', $error_ary);
                }
                else { // set the success message
                    Yii::app()->user->setFlash('success', 'Update successful');
                    Yii::app()->user->setFlash('notice_list', $notice_ary);
                }
            }
            $this->render('index', array('model_list'=>$_POST['TestData']));
        }
	public function actionPreview()
	{
		$this->render('index');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}