<?php

class ProductWithAppsController extends Controller
{
    	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
			//array('allow',  // allow all users to perform 'index' and 'view' actions
			//	'actions'=>array('index','view'),
			//	'users'=>array('*'),
			//),
			//array('allow', // allow authenticated user to perform 'create' and 'update' actions
			//	'actions'=>array('create','update'),
			//	'users'=>array('@'),
			//),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('create','admin','delete','update','index','view'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
                
	}

	public function actionDelete($id)
	{
            $model=$this->loadModel($id);
            // delete the product
            $errors = array();
            $dep_prods = array();
            $rv = $model->delete();
            if (!$rv){
                $errors[]='Unable to delete product';
                $error_ary = $model->getErrors();
                $dep_prods = $model->getDependentProducts();
            }
			else {
				$message = "Successfully deleted {$model->cid}";
			}
            $this->render('delete',array(
                'product'=>$model,        
                'errors' =>$errors,
                'error_ary' =>$error_ary,
                'dep_prods' =>$dep_prod_list,
				'message' => $message,
            ));
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
            $model=$this->loadModel($id);
            /*
		
                if (isset($_POST['action_add_certification'])){
                        $add_cert_error = false;
                        $cert_id = $_POST['add_cert']['id'];
                        if (!empty($cert_id)) {
                            if (!$model->addCertification($cert_id, $_POST['add_cert']['result'])){
                                $add_cert_error = true;
                                $cert = Certifications::model()->findByPk($cert_id);
                                $model->addError('product_id', 'unable to add certification '.$cert->display_name);
                            }
                        }
                        if (!$add_cert_error) {
                            // no redirect we want to stay on this page
                            //$this->redirect(array('view','id'=>$model->app_id));
                        }
                }
                elseif (isset($_POST['action_delete_certifications'])){
                        // delete certifications
                        $delete_error = false;
                        if(isset($_POST['delete_product_certification_id']) && is_array($_POST['delete_product_certification_id'])) {
                            foreach ($_POST['delete_product_certification_id'] as $product_certification_id) {
                                $r = ProductCertifications::model()->findByPk($product_certification_id);
                                if (!$r->delete()) {
                                    $delete_error = true;
                                    $model->addError('app_id', 'unable to delete certification');
                                    foreach ($r->getErrors() as $varname => $error_ary) {
                                        foreach ($error_ary as $errmsg) {
                                            $model->addError('product_id', $errmsg);
                                        }
                                    }
                                }
                            }
                        }
                        if (!$delete_error){
                            // stay on this page
                            //$this->redirect(array('view','id'=>$model->app_id));
                        }
                }
             */
             
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Products']))
		{
                        // delete certifications
                        if(isset($_POST['delete_product_certification_id']) && is_array($_POST['delete_product_certification_id'])) {
                            foreach ($_POST['delete_product_certification_id'] as $product_certification_id) {
                                $pc = ProductCertifications::model()->findByPk($product_certification_id);
                                $pc->delete();
                            }
                        }

                        //foreach ($_POST['CertificationApplications'] as $id => $app_form){
                            
                        //}
                        $is_valid = true;
			$model->attributes=$_POST['Products'];
                        $valid = $model->validate();
                        if ($valid){
                            $rv = $model->setCertifications($_POST['Products']['cert_id_list']);
                            if (!$rv){
                                $model->addError('product_id', "unable to set all of the product certifications");
                                $save_ok = false;
                            }
                        }
                        
                        foreach ($_POST['CertificationApplications'] as $index => $subform) {
                            // load app
                           $app_list[$index] = CertificationApplications::model()->findByPk($subform['app_id']);
                           //$app_list[$index] = $this->_getAppFromModelById($model, $subform['app_id']);
                           $app = $app_list[$index];
                           // if "Requested By" ajax select box (name='user_name') is empty, then stay with default
                           // this is to prevent issue with some one erasing the "combo" box and the hidden field
                           // does not update back to the default.
                           // Normally, this should be a form fix, but I am putting it here first.
                           //
                           if (empty($subform['requested_by'])) {
                              $subform['requested_by'] = $app->requested_by;
                           }
                           $app->attributes = $subform;
                           $valid = $app->validate() && $valid;

                           // save certifications
                           $subform['cert_id_list'] = (!empty($subform['cert_id_list'])) ? $subform['cert_id_list'] : array();
                           $rv = $app->setCertifications($subform['cert_id_list']);
                           if (!$rv) {
                               $app->addError('app_id', 'unable to set all of the certifications correctly');
                               $save_ok = false;
                           }
                        }

                        if ($valid){
                            $save_ok = $model->save(false);
                            foreach ($app_list as $app){
                                $save_ok = $app->save(false) && $save_ok;

                            }                            
                        }
                        
			if($save_ok) {
                            $this->redirect(array('view','id'=>$model->product_id));
                        }
		}

                $apps = $model->certification_applications;
		$this->render('update',array(
			'product'=>$model,
                        'apps'=>$apps,
		));
	}

	public function actionView($id)
	{
            $model = $this->loadModel($id);
            $apps = $model->certification_applications;
            $this->render('view', 
                    array(
                        'model'=>$this->loadModel($id),
                        'apps'=>$apps
                        )
                    );
	}

	

        /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		//$model=Products::model()->findByPk((int)$id);
                $model=Products::model()->with('certification_applications')->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

        private function _getAppFromModelById($model, $app_id){
            $apps = $model->certification_applications;
            foreach ($apps as $app) {
                if ($app_id == $app->app_id){
                    return $app;
                }
            }
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