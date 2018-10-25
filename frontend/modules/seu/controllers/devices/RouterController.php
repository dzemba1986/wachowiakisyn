<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Router;

class RouterController extends DeviceController
{
    protected static function classNameModel() {
        
        return Router::className();
    }
}
