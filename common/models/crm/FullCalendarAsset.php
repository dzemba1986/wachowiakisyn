<?php
namespace common\models\crm;

use Yii;
use yii\web\AssetBundle;

class FullCalendarAsset extends AssetBundle {
    
    public $sourcePath = '@vendor/fullcalendar/fullcalendar/dist';
    
    public $language = 'pl';
    public $autoGenerate = true;
    public $googleCalendar = false;
    
    public $css = [
        'fullcalendar.min.css',
        '/css/fullcalendar/custom_fullcalendar.css',
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

