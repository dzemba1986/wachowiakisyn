<?php

namespace frontend\modules\soa\controllers;

use common\models\soa\Connection;
use common\models\soa\Installation;
use common\models\soa\InstallationSearch;
use Exception;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
            [
                'class' => AjaxFilter::className(),
                'only' => ['create', 'update']
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new InstallationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($connectionId) {
        
        $request = Yii::$app->request;
            
        $installation = new Installation(['scenario' => Installation::SCENARIO_CREATE]);
        $connection = Connection::findOne($connectionId);
        $connection->scenario = Connection::SCENARIO_CREATE_INSTALLATION;
        
        if ($installation->load($request->post()) && $connection->load($request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            
            try {
                $installation->address_id = $connection->address_id;
                $installation->wire_user = implode(",", $installation->wire_user);
                
                if (!($connection->save() && $installation->save()))
                    throw new \Exception('Błąd zapisu instalacji lub połączenia');
                
                $transaction->commit();
        		return 1;
            } catch (\Throwable $t) {
                $transaction->rollBack();
                echo $t->getMessage();
                exit();
            }
        } else {
            return $this->renderAjax('create', [
                'installation' => $installation,
                'connection' => $connection,
                'jsonType' => json_encode($connection->posibleParentTypeIds),
                'deviceListUrl' => '/seu/device/list-from-tree',
                'portListUrl' => '/seu/link/list-port',
                'disableDevicesList' => $connection->type_id <> 2 ? false : true,
            ]);
        }
    }
    
    public function actionUpdate($id)
    {
    	$modelInstallation = $this->findModel($id);
    	$request = Yii::$app->request;
    	
    	if ($request->isAjax){
    		if ($modelInstallation->load(Yii::$app->request->post())) {
    	
    			try {
    				if(!($modelInstallation->save()))
    					throw new Exception('Problem z zapisem połączenia');
    					 
    					return 1;
    			} catch (Exception $e) {
    				return 0;
    			}
    		} else {
    			return $this->renderAjax('update', [
    					'modelInstallation' => $modelInstallation,
    			]);
    		}
    	}
    }
    
    public function actionCrash($connectionId)
    {
    	$modelConnection = Connection::findOne($connectionId);
    	$modelInstallation = Installation::find()->where(['and',
    		['address' => $modelConnection->address], 
    		['type' => $modelConnection->type],
    		['is not', 'wire_date', null],
    		['is not', 'socket_date', null]
    	])->one();
    	
    	$modelConnection->scenario = Connection::SCENARIO_UPDATE;
    	$modelInstallation->scenario = Installation::SCENARIO_UPDATE;
    	
    	$request = Yii::$app->request;
    	
    	if ($request->isAjax){
    		
    		$modelConnection->wire = 0;
    		$modelConnection->socket = 0;
    		
    		$modelInstallation->status = false;
    			
    		try {
    			if(!($modelInstallation->save() && $modelConnection->save()))
    				throw new Exception('Problem z zapisem połączenia i instalacji');
    				
    			return 1;
    		} catch (Exception $e) {
    			return 0;
    		}
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
