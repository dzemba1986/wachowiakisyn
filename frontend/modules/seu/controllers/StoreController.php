<?php

namespace frontend\modules\seu\controllers;

use common\models\seu\devices\DeviceSearch;
use Yii;
use yii\web\Controller;

class StoreController extends Controller {
    
    public function actionIndex() {

        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        $dataProvider->query->andWhere(['address_id' => 1]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}