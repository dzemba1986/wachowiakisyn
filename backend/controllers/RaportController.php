<?php

namespace backend\controllers;

use yii\web\Controller;
use backend\models\ConnectionSearch;
use backend\models\InstallationSearch;
use backend\modules\task\models\InstallTaskSearch;

class RaportController extends Controller
{
    public function actionConnection()
    { 	
    	$request = \Yii::$app->request;
    	
    	$searchModel = new ConnectionSearch(); 
    	$dataProvider = $searchModel->search($request->queryParams);
    	
    	
    	if(!empty($request->queryParams['ConnectionSearch']['minConfDate']) && !empty($request->queryParams['ConnectionSearch']['maxConfDate'])){
	    	$dataProvider->query->andWhere(['or', [
    			'and', ['is not', 'conf_date', null], ['connection.type_id' => 1], ['nocontract' => false], ['>=', 'conf_date', $request->queryParams['ConnectionSearch']['minConfDate']], ['<=', 'conf_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
	    	],[
    			'and', ['is not', 'pay_date', null], ['connection.type_id' => 2], ['nocontract' => false], ['>=', 'pay_date', $request->queryParams['ConnectionSearch']['minConfDate']], ['<=', 'pay_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
	    	],[
	    	    'and', ['is not', 'conf_date', null], ['connection.type_id' => 3], ['nocontract' => false], ['>=', 'conf_date', $request->queryParams['ConnectionSearch']['minConfDate']], ['<=', 'conf_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
	    	]]);
    	} elseif(!empty($request->queryParams['ConnectionSearch']['minConfDate']) && empty($request->queryParams['ConnectionSearch']['maxConfDate'])){
    		$dataProvider->query->andWhere(['or', [
				'and', ['is not', 'conf_date', null], ['connection.type_id' => 1], ['nocontract' => false], ['>=', 'conf_date', $request->queryParams['ConnectionSearch']['minConfDate']]
    		],[
				'and', ['is not', 'pay_date', null], ['connection.type_id' => 2], ['nocontract' => false], ['>=', 'pay_date', $request->queryParams['ConnectionSearch']['minConfDate']]
    		],[
    		    'and', ['is not', 'conf_date', null], ['connection.type_id' => 3], ['nocontract' => false], ['>=', 'conf_date', $request->queryParams['ConnectionSearch']['minConfDate']]
    		]]);
    	} elseif(empty($request->queryParams['ConnectionSearch']['minConfDate']) && !empty($request->queryParams['ConnectionSearch']['maxConfDate'])){
    		$dataProvider->query->andWhere(['or', [
				'and', ['is not', 'conf_date', null], ['connection.type_id' => 1], ['nocontract' => false], ['<=', 'conf_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
    		],[
				'and', ['is not', 'pay_date', null], ['connection.type_id' => 2], ['nocontract' => false], ['<=', 'pay_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
    		],[
    		    'and', ['is not', 'conf_date', null], ['connection.type_id' => 3], ['nocontract' => false], ['<=', 'conf_date', $request->queryParams['ConnectionSearch']['maxConfDate']]
    		]]);
    	} else {
    		$dataProvider->query->andWhere(['or', [
				'and', ['is not', 'conf_date', null], ['connection.type_id' => 1], ['nocontract' => false]
    		],[
				'and', ['is not', 'pay_date', null], ['connection.type_id' => 2], ['nocontract' => false]
    		],[
    		    'and', ['is not', 'conf_date', null], ['connection.type_id' => 3], ['nocontract' => false]
    		]]);
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
    
    public function actionTask()
    {
    	$searchModel = new InstallTaskSearch();
    	$dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
    	
    	$dataProvider->sort->defaultOrder = ['end' => SORT_ASC];
    	$dataProvider->query->andWhere([
    		'and',
    	    ['task.status' => true],
    	    ['or', ['task.type_id' => 3], ['and', ['task.type_id' => [1,2]], ['>', 'cost', 0]]],
    		['is not', 'close', null]	
    	]);
    	
    	return $this->render('grid_task', [
    		'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    	]);
    }
}
