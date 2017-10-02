<?php

namespace backend\controllers;

use Yii;
use backend\models\Address;
use backend\models\AddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\AddressShort;
use backend\models\AddressShortSearch;

/**
 * AddressController implements the CRUD actions for Address model.
 */
class AddressController extends Controller
{
	/**
	 * 
	 * {@inheritDoc}
	 * @see \yii\base\Component::behaviors()
	 */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post']
                ],
            ],
        ];
    }

    /**
     * Lists all Address models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lista all AddressShort models
     * @return string
     */
    public function actionList()
    {
    	$searchModel = new AddressShortSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	
    	return $this->render('list', [
    		'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    	]);
    }

    /**
     * Creates a new Address model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    	//TODO dodawanie nowej ulicy do bazy
    }

    /**
     * Updates an existing Address model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
    	$request = Yii::$app->request;
    		
    	if ($request->isAjax){
    		$model = $this->findModel($id);
    		
    		try {
    			if ($model->load($request->post())) {
    				if ($model->save())
    					return 1;
    				else 
    					return 0;
    			} else {
    				return $this->renderAjax('update', [
    					'model' => $model,
    				]);
    			}
    		} catch (Exception $e) {
 				echo $e->getMessage();   			
    		}
    	} else { //TODO może by trzeba było obsłużyć jakoś zapytanie nie ajax'owe?
    		echo "Zapytanie nie ajax'owe";
    	}
    }
    
    public function actionUpdateShort($id)
    {
    	$request = Yii::$app->request;
    	
    	if ($request->isAjax){
    		$model = AddressShort::findOne($id);
    		
    		try {
    			if ($model->load($request->post())) {
    				if ($model->save())
    					return 1;
    				else
    					return 0;
    			} else {
    				return $this->renderAjax('update_short', [
    					'model' => $model
    				]);
    			}
    		} catch (Exception $e) {
    			echo $e->getMessage();
    		}
    	} else { //TODO może by trzeba było obsłużyć jakoś zapytanie nie ajax'owe?
    		echo "Zapytanie nie ajax'owe";
    	}
    }

    /**
     * Deletes an existing Address model.
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
     * Finds the Address model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Address the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
