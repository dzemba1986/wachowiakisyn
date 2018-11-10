<?php

namespace frontend\modules\history\controllers;

use common\models\history\History;
use common\models\history\HistoryIpSearch;
use Yii;
use yii\web\Controller;

class HistoryController extends Controller {
    
    function actionConnection($id) {
        
        $histories = History::find()->joinWith('user')
            ->select('history.created_at, created_by, last_name, desc')
            ->where(['connection_id' => $id])->all();
        
        return $this->renderAjax('connection', [
            'histories' => $histories
        ]);
    }
    
    function actionDevice($id) {
        
        $histories = History::find()->joinWith('user')
        ->select('history.created_at, created_by, last_name, desc')
        ->where(['device_id' => $id])->orderBy('created_at DESC')->asArray()->all();
        
        return $this->renderAjax('device', [
            'histories' => $histories
        ]);
    }
    
    public function actionIp(){
        
        $historyIp = new HistoryIpSearch();
        $dataProvider = $historyIp->search(Yii::$app->request->queryParams);
        
        return $this->render('ip', [
            'historyIp' => $historyIp,
            'dataProvider' => $dataProvider,
        ]);
    }
}
