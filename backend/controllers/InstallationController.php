<?php

namespace backend\controllers;

use Yii;
use backend\models\Installation;
use backend\models\InstallationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Connection;
use yii\base\Exception;
/**
 * InstallationController implements the CRUD actions for Installation model.
 */
class InstallationController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Installation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InstallationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Installation model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Installation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /* public function actionCreate()
    {
        $model = new Installation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    } */
    
    /**
     * Creates a new Installation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($conId)
    {
    	$modelInstallation = new Installation();
    	$modelConnection = Connection::findOne($conId);
    	$modelConnection->scenario = Connection::SCENARIO_CREATE_INSTALLATION;
    	$modelInstallation->scenario = Installation::SCENARIO_CREATE;
    
        $request = Yii::$app->request;
        
        if ($request->isAjax){
        	
            if ($modelInstallation->load($request->post()) && $modelConnection->load($request->post())) {
			
            	$transaction = Yii::$app->db->beginTransaction();
            	
            	try {
            		$modelInstallation->type = $modelConnection->getInstallationType();
            		$modelInstallation->address = $modelConnection->address;
            		$modelInstallation->wire_user = implode(",", $modelInstallation->wire_user);
            		
            		if(!($modelConnection->save() && $modelInstallation->save()))
            			throw new Exception('Problem z save"em');
            		
            		$transaction->commit();	
            		return 1;
            	} catch (Exception $e) {
            		$transaction->rollBack();
            		//var_dump($modelConnection->save());
            	}

            } else {
                return $this->renderAjax('create', [
                        'modelInstallation' => $modelInstallation,
                        'modelConnection' => $modelConnection,
                ]);
            }
        }
    }

    /**
     * Updates an existing Installation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Installation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Installation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Installation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Installation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
