<?php
namespace components\jstree;

use yii\web\AssetBundle;

class JsTreeAsset extends AssetBundle {
    
    public $sourcePath = '@components/jstree';
    
    public $css = [
        'jstree-built.css',
    ];
   
    public $js = [
        'jstree-built.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}