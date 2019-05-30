<?php
namespace common\models\crm;

use yii\web\AssetBundle;

class PrintAsset extends AssetBundle {
    
    public $sourcePath = '@vendor/fullcalendar/fullcalendar/dist';
    
    public $css = [
        'fullcalendar.print.min.css'
    ];
    
    public $cssOptions = [
        'media' => 'print'
    ];
}

