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
use yii\db\Query;

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
     * Creates a new AddressShort model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    	$request = Yii::$app->request;
    	
    	if ($request->isAjax){
    		$model = new AddressShort();
    		
    		try {
    			if ($model->load($request->post())) {
    				$model->config = (int) $model->config;	//z POST'a pobiera jako string
    				if ($model->save())
    					return 1;
    				else
    					return var_dump($model->firstErrors);
    			} else {
    				return $this->renderAjax('create_short', [
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
     * Lists all street in Poznań for `select2` widget
     * @param string $q search string
	 * @return string $out JSON 
     */
    public function actionTerytList($q){
    	
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	
    	$out = ['results' => ['id' => '', 'concat' => '']];
    	
    	if (!is_null($q)) {
    		
    		// ...AS id... potrzebne by była możliwość wybrania rezultatów
    		$query = new Query();
    		$query->select(['sym_ul AS id', new \yii\db\Expression("
	    		CONCAT(cecha, ' ', nazwa_2, ' ', nazwa_1)
	    	"), 'sym', 'gmi', 'cecha', 'nazwa_1', 'nazwa_2'])
	    	->from('ulic')
	    	->where(['and', ['woj' => '30'], ['pow' => '64'], ['rodz_gmi' => '9'], ['like', 'nazwa_1', $q]])
    		->limit(10);
	    		
			$command = $query->createCommand();
	 		$data = $command->queryAll();
	    	$out['results'] = array_values($data);
    	}
    	
    	return $out;
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
