<?php
namespace common\models\crm;

use yii\web\AssetBundle;

class PrintAsset extends AssetBundle {
    
    public $basePath = '@webroot/js/fullcalendar_new';
    public $baseUrl = '@web/js/fullcalendar_new';
    
    public $css = [
        'fullcalendar.print.min.css'
    ];
    
    public $cssOptions = [
        'media' => 'print'
    ];
}

