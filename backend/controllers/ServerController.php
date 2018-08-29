<?php

namespace backend\controllers;

use backend\models\Server;

class ServerController extends DeviceController
{	
    protected static function getModel() {
        
        return new Server();
    }
}
