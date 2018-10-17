<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\Server;

class ServerController extends DeviceController
{	
    protected static function getModel() {
        
        return new Server();
    }
}
