<?php

use yii\bootstrap\Tabs;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var backend\modules\address\models\Address $address
 */

$this->title = $address;
$this->params['breadcrumbs'][] = 'Adresy';
$this->params['breadcrumbs'][] = $address;

echo Tabs::widget([
    'encodeLabels' => false,
    'tabContentOptions' => ['style' => 'padding: 10px;'],
    'items'=> [
        [
            'label' => 'Dane',
            'active' => true,
            'linkOptions' => ['data-url' => Url::to(['address/view', 'id' => $address->id])]
        ],
        [
            'label' => 'Instalacje',
            'linkOptions' => ['data-url' => Url::to(['address/installs', 'id' => $address->id])]
        ],
        [
            'label' => 'Zgłoszenia',
            'linkOptions' => ['data-url' => Url::to(['address/tasks', 'id' => $address->id])]
        ],
    ]
]);

$js = <<<JS
$(function() {
    $("#w0-tab0").load($("a[href='#w0-tab0']").attr('data-url'));
    $('[data-toggle="tab"]').click(function(e) {
        var _this = $(this),
            loadurl = _this.attr('data-url'),
            targ = _this.attr('href');
    
        $(targ).load(loadurl);
    
        _this.tab('show');
        return false;
    });
});
JS;

$this->registerJs($js);
?>