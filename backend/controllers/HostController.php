<?php

namespace backend\controllers;

use backend\models\Host;
use yii\web\NotFoundHttpException;

class HostController extends DeviceController
{
    function actionGetMac($id) {
        
        $host = $this->findModel($id);
        return $host->mac;
    }
    
    function actionGetUrlCheckDhcp($id) {
        
        $host = $this->findModel($id);
        return "http://172.20.4.17:701/index.php?sourceid=3&filter=clientmac%3A%3D" . base_convert(preg_replace('/:/', '', $host->mac), 16, 10) . "&search=Search";
    }
    
    protected function findModel($id)
    {
        if (($model = Host::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
