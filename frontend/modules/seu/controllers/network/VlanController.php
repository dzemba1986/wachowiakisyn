<?php

namespace frontend\modules\seu\controllers\network;

use common\models\seu\network\Vlan;
use common\models\seu\network\VlanSearch;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AddressController implements the CRUD actions for Address model.
 */
class VlanController extends Controller
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
     * Lists all Address models.
     * @return mixed
     */
    public function actionGrid()
    {
        $modelVlan = new VlanSearch();
     	$dataProvider = $modelVlan->search(Yii::$app->request->queryParams);

        return $this->render('grid', [
            'modelVlan' => $modelVlan,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionCreate()
    {
    	$modelVlan = new Vlan();
    	$modelVlan->scenario = Vlan::SCENARIO_CREATE;
    	 
    	$request = Yii::$app->request;
    
    	if ($request->isAjax){
    		if ($modelVlan->load($request->post())) {
    			if($modelVlan->validate()){
    				try {
    					if(!$modelVlan->save())
    						throw new Exception('Problem z zapisem vlanu');
    					return 1;
    				} catch (Exception $e) {
    					var_dump($modelVlan->errors);
    					var_dump($e->getMessage());
    					exit();
    				}
    				 
    			} else {
    				var_dump($modelVlan->errors);
    				exit();
    			}
    		} else {
    			return $this->renderAjax('create', [
    				'modelVlan' => $modelVlan,
    			]);
    		}
    	}
    }
	
    public function actionUpdate($id)
    {
    	$modelVlan = $this->findModel($id);
    	$modelVlan->scenario = Vlan::SCENARIO_UPDATE;
    
    	$request = Yii::$app->request;
    
    	if($request->isAjax){
    		if($modelVlan->load($request->post())){
    			if($modelVlan->validate()){
    				try {
    					if(!$modelVlan->save())
    						throw new Exception('Problem z zapisem vlanu');
    					return 1;
    				} catch (Exception $e) {
    					var_dump($modelVlan->errors);
    					exit();
    				}
    			}
    		} else {
    			return $this->renderAjax('update', [
    					'modelVlan' => $modelVlan
    			]);
    		}
    	}
    }
    
    public function actionDelete($id)
    {
    	if($id){
    		if (count($this->findModel($id)->modelSubnets) > 0)
    			return 'Vlan wykorzystywany';
    		else
    			$this->findModel($id)->delete();
    		
    		return 1;
    	} else 
    		return 0;
    }
    
    protected function findModel($id)
    {
        if (($model = Vlan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
