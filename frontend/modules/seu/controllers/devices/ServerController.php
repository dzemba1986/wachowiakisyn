<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Server;

class ServerController extends DeviceController
{	
    protected static function classNameModel() {
        
        return Server::className();
    }
}
