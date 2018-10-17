<?php

namespace frontend\modules\task;

use yii\web\AssetBundle;

class CrmAsset extends AssetBundle
{
	public $sourcePath = '@bacend/modules/task/assets';
	
	public $js = [
		'js/fullcalendar/fullcalendar.js'
	];
	
	public $css = [
		'css/fullcalendar/fullcalendar.css'
	];
	
	public $depends = [
		'yii\web\JqueryAsset',
		'yii\web\YiiAsset'
	];
}
