<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Router;

class RouterController extends DeviceController
{
    protected static function getModelClassName() {
        
        return Router::className();
    }
    
    protected static function getModel() {
        
        return new Router();
    }
}
