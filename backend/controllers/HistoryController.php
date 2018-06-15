<?php

namespace backend\controllers;

use backend\models\History;
use yii\web\Controller;

class HistoryController extends Controller {
    
    function actionHistoryByDevice($deviceId) {
        
        $histories = History::find()->joinWith('user')
            ->select('history.created_at, created_by, last_name, desc')
            ->where(['device_id' => $deviceId])->orderBy('created_at DESC')->asArray()->all();
        
        return $this->renderAjax('history', [
            'histories' => $histories
        ]);
    }
    
    function actionHistoryByConnection($connectionId) {
        
        $histories = History::find()->joinWith('user')
            ->select('history.created_at, created_by, last_name, desc')
            ->where(['connection_id' => $connectionId])->asArray()->all();
        
        return $this->renderAjax('history', [
            'histories' => $histories
        ]);
    }
}
