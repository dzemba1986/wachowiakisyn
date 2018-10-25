<?php
namespace common\models\crm;

use yii\web\AssetBundle;

class MomentAsset extends AssetBundle {

    public $basePath = '@webroot/js/fullcalendar_new';
    public $baseUrl = '@web/js/fullcalendar_new';

    public $js = [
        'lib/moment.min.js'
    ];
}

