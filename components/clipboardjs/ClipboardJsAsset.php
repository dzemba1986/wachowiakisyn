<?php
namespace components\clipboardjs;

use yii\web\AssetBundle;

class ClipboardJsAsset extends AssetBundle {
    
    public $sourcePath = '@components/clipboardjs';
    
    public $js = [
        'clipboardjs-built.js',
    ];
}