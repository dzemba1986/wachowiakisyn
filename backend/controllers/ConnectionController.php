<?php

namespace backend\controllers;

use Yii;
use backend\models\Connection;
use backend\models\ConnectionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ConnectionController implements the CRUD actions for Connection model.
 */
class ConnectionController extends Controller
{
    public function behaviors()
    {
        return [
        	'access' => [
        		'class' => AccessControl::className(),
        		//'only' => ['create', 'delete', 'index', 'update'],
        		'rules'	=> [
        			[
        				'allow' => true,
        				'actions' => ['create', 'delete', 'index', 'update', 'view'],
        				'roles' => ['@']	
        			]	
        		]
        	],	
        		
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Connection models.
     * @return mixed
     */
    public function actionIndex($mode = 'nopay')
    {
        $searchModel = new ConnectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        switch ($mode){
        	case 'nopay':
        		$dataProvider->query->joinWith('modelTask')->andWhere([
        			'pay_date' => null, 
        			Connection::tableName().'.close_date' => null
        		])->orderBy(['start_date' => SORT_DESC]);
        		break;
        	case 'install':
        		$dataProvider->query->joinWith('modelTask')->andWhere([
        			'wire' => 0, 
        			Connection::tableName().'.close_date' => null
        		]);
        		break;
        	case 'conf':
        		$dataProvider->query->andWhere([
        			'conf_date' => NULL, 
        			'close_date' => NULL, 
        			Connection::tableName().'.type' => 2 
        		])->andWhere(['>', 'wire', 0])->andWhere(['>', 'socket', 0])->andWhere(['is not', 'mac', null]);
        		break;
        	case 'off':
        		$dataProvider->sort = ['defaultOrder' => ['close_date' => SORT_ASC]];
        		$dataProvider->query->andWhere(['is not', 'close_date', null]);
        		break;
        	case 'pay':
        		$dataProvider->query->andWhere(['is not', 'activ_date', null])->andWhere(['close_date' => null]);
        		break;
        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mode' => $mode,
        ]);
    }

    /**
     * Displays a single Connection model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
    	if(Yii::$app->request->isAjax)
    	
            return $this->renderAjax('view', [
                'modelConnection' => $this->findModel($id),
            ]);
    }

    /**
     * Creates a new Connection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Connection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Connection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $modelConnection = $this->findModel($id);
        $request = Yii::$app->request;

        if ($request->isAjax){
	        if ($modelConnection->load(Yii::$app->request->post())) {
	        	
	        	try {
	        		if(!($modelConnection->save()))
	        			throw new Exception('Problem z zapisem połączenia');
	        		
        			return 1;
	        	} catch (Exception $e) {
	        		return 0;
	        	}
	        } else {
	            return $this->renderAjax('update', [
	                'modelConnection' => $modelConnection,
	            ]);
	        }
        }
    }

    /**
     * Deletes an existing Connection model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    protected function findModel($id)
    {
        if (($model = Connection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
