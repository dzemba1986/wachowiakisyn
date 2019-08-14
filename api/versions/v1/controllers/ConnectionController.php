<?php

namespace app\versions\v1\controllers;

use app\versions\v1\models\ConnectionResource;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;


class ConnectionController extends ActiveController {
    
	public $modelClass = ConnectionResource::class;
	
// 	public function actions() {
	    
// 	    $actions = parent::actions();
// 	    unset($actions['delete']);
	    
// 	    return array_merge(
// 	        $actions,
// 	        [
// 	            'index' => [
// 	                'class' => 'yii\rest\IndexAction',
// 	                'modelClass' => $this->modelClass,
// 	                'checkAccess' => [$this, 'checkAccess'],
// 	                'prepareDataProvider' => function ($action) {
//             	        $model = new $this->modelClass;
//             	        $query = $model::find()->select('id, soa_id, address_id');
//             	        $dataProvider = new ActiveDataProvider(['query' => $query]);
//             	        $model->setAttribute('soa_id', @$_GET['soa_id']);
//             	        $query->andFilterWhere(['soa_id' => $model->soa_id]);
//             	        return $dataProvider;
// 	                }
//                 ]
//             ]
//         );
// 	    return $actions;
// 	}

	public function actionIndex()
	{
	    return new ActiveDataProvider([
	        'query' => self::find(),
	    ]);
	}
}