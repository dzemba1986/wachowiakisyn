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
    	$searchModel = new ConnectionSearch();
    	$dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
    	
    	$dataProvider->query->andWhere([
    		'is not', 'conf_date', null
    	]);
    	
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
