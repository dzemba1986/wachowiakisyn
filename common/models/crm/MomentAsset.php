<?php
namespace common\models\crm;

use yii\web\AssetBundle;

class MomentAsset extends AssetBundle {

    public $sourcePath = '@vendor/moment/moment';

    public $js = [
        'moment.js'
    ];
}

