<?php

namespace backend\controllers;

use backend\models\MediaConverter;

class MediaConverterController extends DeviceController
{
    protected static function getModel() {
        
        return new MediaConverter();
    }
}
