<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Radio;

class RadioController extends DeviceController {
    
    protected static function classNameModel() {
        
        return Radio::className();
    }
}
