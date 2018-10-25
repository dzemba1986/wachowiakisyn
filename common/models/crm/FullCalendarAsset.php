<?php
namespace common\models\crm;

use Yii;
use yii\web\AssetBundle;

class FullCalendarAsset extends AssetBundle {
    
    public $basePath = '@webroot/js/fullcalendar_new';
    public $baseUrl = '@web/js/fullcalendar_new';
    
    public $language = 'pl';
    public $autoGenerate = true;
    public $googleCalendar = false;
    
    public $css = [
        'fullcalendar.min.css',
    ];
    
    public $js = [
        'fullcalendar.min.js',
        'locale-all.js',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'common\models\crm\MomentAsset',
        'common\models\crm\PrintAsset'
    ];
    
    public function registerAssetFiles($view) {
        
        $language = $this->language ? $this->language : Yii::$app->language;
        if (strtoupper($language) != 'EN-US')
        {
            $this->js[] = "locale/{$language}.js";
        }
        if($this->googleCalendar) {
            
            $this->js[] = 'gcal.js';
        }
        parent::registerAssetFiles($view);
    }
    
}

