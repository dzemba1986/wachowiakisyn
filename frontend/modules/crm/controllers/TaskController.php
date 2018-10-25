<?php

namespace frontend\modules\crm\controllers;

use common\models\crm\Task;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TaskController extends Controller {
    
    protected function findModel($id) {
        
        if (($model = Task::findOne($id)) !== null) return $model;
        else throw new NotFoundHttpException('The requested page does not exist.');
    }
}
