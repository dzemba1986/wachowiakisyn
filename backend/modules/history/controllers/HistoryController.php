<?php

namespace backend\modules\history\controllers;

use common\models\history\History;
use yii\web\Controller;

class HistoryController extends Controller {
    
    function actionHistoryByDevice($id) {
        
        $histories = History::find()->joinWith('user')
            ->select('history.created_at, created_by, last_name, desc')
            ->where(['device_id' => $id])->orderBy('created_at DESC')->asArray()->all();
        
        return $this->renderAjax('device_history', [
            'histories' => $histories
        ]);
    }
    
    function actionHistoryByConnection($id) {
        
        $histories = History::find()->joinWith('user')
            ->select('history.created_at, created_by, last_name, desc')
            ->where(['connection_id' => $id])->asArray()->all();
        
        return $this->renderAjax('connection_history', [
            'histories' => $histories
        ]);
    }
}
