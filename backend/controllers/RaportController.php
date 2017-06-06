<?php

namespace backend\controllers;

use yii\web\Controller;
use backend\models\ConnectionSearch;
use backend\models\InstallationSearch;

class RaportController extends Controller
{
    /**
     * Lists all Modyfication models.
     * @return mixed
     */
    public function actionConnection()
    { 	
    	$request = \Yii::$app->request;
    	
    	$searchModel = new ConnectionSearch(); 
    	$dataProvider = $searchModel->search($request->queryParams);
    	
    	
    	if(!empty($request->queryParams['ConnectionSearch']['minConfDate']) && !empty($request->queryParams['ConnectionSearch']['maxConfDate'])){
	    	$dataProvider->query->andWhere([
	    		'and', ['is not', 'conf_date', null], ['type' => 1], ['>=', 'conf_date', $request->queryParams['ConnectionSearch']['minConfDate']], ['<=', 'conf_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
	    	])->orWhere([
	    		'and', ['is not', 'pay_date', null], ['type' => 2], ['>=', 'pay_date', $request->queryParams['ConnectionSearch']['minConfDate']], ['<=', 'pay_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
	    	]);
    	} elseif(!empty($request->queryParams['ConnectionSearch']['minConfDate']) && empty($request->queryParams['ConnectionSearch']['maxConfDate'])){
    		$dataProvider->query->andWhere([
    				'and', ['is not', 'conf_date', null], ['type' => 1], ['>=', 'conf_date', $request->queryParams['ConnectionSearch']['minConfDate']]
    		])->orWhere([
    				'and', ['is not', 'pay_date', null], ['type' => 2], ['>=', 'pay_date', $request->queryParams['ConnectionSearch']['minConfDate']]
    		]);
    	} elseif(empty($request->queryParams['ConnectionSearch']['minConfDate']) && !empty($request->queryParams['ConnectionSearch']['maxConfDate'])){
    		$dataProvider->query->andWhere([
    				'and', ['is not', 'conf_date', null], ['type' => 1], ['<=', 'conf_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
    		])->orWhere([
    				'and', ['is not', 'pay_date', null], ['type' => 2], ['<=', 'pay_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
    		]);
    	} else {
    		$dataProvider->query->andWhere([
    				'and', ['is not', 'conf_date', null], ['type' => 1]
    		])->orWhere([
    				'and', ['is not', 'pay_date', null], ['type' => 2]
    		]);
    	}
    	
        return $this->render('grid_connection', [
            'searchModel' => $searchModel,
        	'dataProvider' => $dataProvider,	
        ]);
    }
    
    public function actionInstallation()
    {
    	$searchModel = new InstallationSearch();
    	$dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
    	
    	$dataProvider->query->andWhere([
    		'is not', 'socket_date', null
    	]);
    	 
    	return $this->render('grid_installation', [
    		'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    	]);
    }
}
