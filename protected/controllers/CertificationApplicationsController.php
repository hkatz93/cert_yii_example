<?php

class CertificationApplicationsController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all authenticated users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('@'),
            ),
            //array('allow', // allow authenticated user to perform 'create' and 'update' actions
            //	'actions'=>array('create','update'),
            //	'users'=>array('@'),
            //),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('create', 'admin', 'delete', 'update', 'AutoCompleteLookupUsername', 'AddCertification', 'AutoCompleteLookupCompanyContact',
                    'UpdateDependentProducts', 'ExportToDependentProduct'),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
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

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        throw new Exception("create has been disabled");
        $model = new CertificationApplications;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['CertificationApplications'])) {
            $model->attributes = $_POST['CertificationApplications'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->app_id));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        if (isset($_POST['action_add_certification'])) {
            $add_cert_error = false;
            $cert_id = $_POST['add_cert']['id'];
            if (!empty($cert_id)) {
                if (!$model->addCertification($cert_id, $_POST['add_cert']['result'])) {
                    $add_cert_error = true;
                    $cert = Certifications::model()->findByPk($cert_id);
                    $model->addError('app_id', 'unable to add certification ' . $cert->display_name);
                }
            }
            if (!$add_cert_error) {
                // no redirect we want to stay on this page
                //$this->redirect(array('view','id'=>$model->app_id));
            }
        } elseif (isset($_POST['action_delete_certifications'])) {
            // delete certifications
            $delete_error = false;
            if (isset($_POST['delete_request_id']) && is_array($_POST['delete_request_id'])) {
                foreach ($_POST['delete_request_id'] as $request_id) {
                    $r = RequestedCertifications::model()->findByPk($request_id);
                    if (!$r->delete()) {
                        $delete_error = true;
                        $model->addError('app_id', 'unable to delete certification (request_id=' . $request_id . ')');
                        foreach ($r->getErrors() as $varname => $error_ary) {
                            foreach ($error_ary as $errmsg) {
                                $model->addError('app_id', $errmsg);
                            }
                        }
                    }
                }
            }
            if (!$delete_error) {
                // stay on this page
                //$this->redirect(array('view','id'=>$model->app_id));
            }
        }
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        elseif (isset($_POST['CertificationApplications'])) {

            // if "Requested By" ajax select box (name='user_name') is empty, then stay with default
            // this is to prevent issue with some one erasing the "combo" box and the hidden field
            // does not update back to the default.
            // Normally, this should be a form fix, but I am putting it here first.
            //
                        $user_name = trim($_POST['user_name']);
            if (empty($user_name)) {
                $_POST['CertificationApplications']['requested_by'] = $model->requested_by;
            }

            $model->attributes = $_POST['CertificationApplications'];


            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->app_id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Deletes a requested certification for this application
     * @param integer $request_id the ID of the requested_certifications row
     */
    public function actionDeleteRequestedCertification($request_id) {

        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $rv = RequestedCertifications::model()->findByPk($request_id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax'])) {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
        } else {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * pushes the application changes from the parent to the child products
     * @param integer $id identifies the application id whose certifications we will copy to the dependents
     */
    public function actionUpdateDependentProducts($app_id) {
       //if (Yii::app()->request->isPostRequest) {
            //$model = $this->loadModel($app_id); 
            
            $parent_app = CertificationApplications::model()->findByPk($app_id);
            if (!($parent_app instanceof CertificationApplications)) {
                throw new InvalidArgumentException('app_id does not retrieve a valid CertificationApplications object');
            }
            $parent_prod = Products::model()->findByPk($parent_app->product_id);
            //print "updating the following dependent products: (product_id)\n";
            //print "========================================================\n";
            $dep_prod_list = $parent_prod->getDependentProducts();
            if (count($dep_prod_list) == 0) {
                //print "-- [No dependent products were found for product_id={$parent_prod->product_id}] --\n\n";
            }

            $i = 1;
            $errors = array();
            foreach ($dep_prod_list as $dep_prod) {
                //print $i++;  print ": " . $dep_prod->product_id;  print "\n";            //print "\t applying import...\n";
                $rv = $dep_prod->appendParentApplicationResults($app_id);
               
                if (!$rv) {
                    $errors[] = $dep_prod->getErrors();
                    //print "\t errors = ";               //print_r($errors);
                   // $model->addError("module_app_id', 'unable to add this application to the dependent (dependent app_id = {$dep_prod->app_id} ");
                }
            }
            if (!empty($errors)) {
                throw new CHttpException(500, 'Server Error(s), there were some problems with the updates.'.(print_r($errors,true)));
            }
            // put redrect at end of function
            // if AJAX request  we should not redirect the browser
            if (!isset($_GET['ajax'])) {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
            
        //} else {
        //    throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        //}
    }

  /**
     * pushes the application changes from the parent to the child products
     * @param integer $app_id identifies the application id whose certifications we will copy to the dependents
   * @param array $dep_prod_id_list is the product_id of the dependent product
     */
	public function actionExportToDependentProduct($app_id, $dep_prod_id) {
		$errors = array();
		if (!preg_match('/^\d+$/', $app_id)) {
			throw new InvalidArgumentException('app_id is not an integer');
		}
		if (!preg_match('/^\d+$/', $dep_prod_id)) {
			throw new InvalidArgumentException('dep_prod_id is not an integer');
		}
		$parent_app = CertificationApplications::model()->findByPk($app_id);
		if (!($parent_app instanceof CertificationApplications)) {
			throw new InvalidArgumentException('app_id does not retrieve a valid CertificationApplications object');
		}
		$parent_prod = Products::model()->findByPk($parent_app->product_id);
		
		// load dep product
		$dep_prod = Products::model()->findByPk($dep_prod_id);
		$rv = $dep_prod->importParentApplication($app_id);
		 if (!$rv) {
			$errors[] = $dep_prod->getErrors();			
		}
		 if (!empty($errors)) {
			throw new CHttpException(500, 'Server Error(s), there were some problems with the updates.'.(print_r($errors,true)));
		}
		// put redrect at end of function
		// if AJAX request  we should not redirect the browser
		if (!isset($_GET['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
	}
  
    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('CertificationApplications');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new CertificationApplications('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['CertificationApplications']))
            $model->attributes = $_GET['CertificationApplications'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = CertificationApplications::model()->findByPk((int) $id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'certification-applications-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionAutoCompleteLookupUsername() {
        if (Yii::app()->request->isAjaxRequest && isset($_GET['q'])) {
            /* q is the default GET variable name that is used by
              / the autocomplete widget to pass in user input
             */
            $name = $_GET['q'];
            // this was set with the "max" attribute of the CAutoComplete widget
            $limit = min($_GET['limit'], 50);
            $criteria = new CDbCriteria;
            $criteria->order = "username";
            $criteria->condition = "username LIKE :sterm";
            $criteria->params = array(":sterm" => "%$name%");
            $criteria->limit = $limit;
            $userArray = Users::model()->findAll($criteria);
            $returnVal = '';
            foreach ($userArray as $userAccount) {
                $returnVal .= $userAccount->getAttribute('username') . '|'
                        . $userAccount->getAttribute('user_id') . "\n";
            }
            echo $returnVal;
        }
    }

    public function actionAutoCompleteLookupCompanyContact() {
        if (Yii::app()->request->isAjaxRequest && isset($_GET['q'])) {
            /* q is the default GET variable name that is used by
              / the autocomplete widget to pass in user input
             */

            $name = $_GET['q'];

            // this was set with the "max" attribute of the CAutoComplete widget
            $limit = min($_GET['limit'], 50);
            $rows = CertificationApplications::getCompanyContactsLikeNameList($name, $limit);

            //$criteria = new CDbCriteria;
            //$criteria->distinct = true;
            //$criteria->order = "company_contact";
            //$criteria->condition = "company_contact LIKE :sterm";
            //$criteria->params = array(":sterm"=>"%$name%");
            //$criteria->limit = $limit;
            //$userArray = CertificationApplications::model()->findAll($criteria);
            $returnVal = '';
            foreach ($rows as $row) {
                $returnVal .= $row['company_contact'] . "|" . $row['company_contact'] . "\n";
                //$returnVal .= $userAccount->getAttribute('company_contact').'|'
                //    .$userAccount->getAttribute('company_contact')."\n";
            }
            echo $returnVal;
        }
    }

    public function actionAddCertification($id) {
        throw new Exception("got it");
        $model = $this->loadModel($id);
        if (isset($_POST['add_cert'])) {
            if (is_array($_POST['add_cert'])) {
                throw new Exception("got it");
            }
        }
    }

}
