<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\Router;

class RouterController extends DeviceController
{
    protected static function getModel() {
        
        return new Router();
    }
}
