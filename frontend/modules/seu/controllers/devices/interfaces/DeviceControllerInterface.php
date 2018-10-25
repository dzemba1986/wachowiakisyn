<?php
namespace frontend\modules\seu\controllers\devices\interfaces;

interface DeviceControllerInterface {
    
    public function actionValidation();
    public function actionView($id);
    public function actionUpdate($id);
}

