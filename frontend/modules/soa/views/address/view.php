<?php

use common\models\soa\Connection;
use common\models\soa\Internet;
use common\models\soa\Phone;
use common\models\soa\Tv;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\SerialColumn;

/**
 * @var yii\web\View $this
 * @var backend\modules\address\models\Address $address
 */

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md-3']);
        echo DetailView::widget([
            'model' => $address,
            'formatter' => [
                'class' => 'yii\i18n\Formatter',
                'nullDisplay' => ''
            ],
            'attributes' => [
                'ulica',
                'dom',
                'dom_szczegol',
                'lokal',
                'lokal_szczegol',
            ],
        ]);
    echo Html::endTag('div');
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md-4']);
        echo GridView::widget([
            'id' => 'connection-grid',
            'summary' => false,
            'pjax' => false,
            'resizableColumns' => false,
            'export' => false,
            'dataProvider' => $connections,
            'columns' => [
                [
                    'header' => 'Lp.',
                    'class' => SerialColumn::class,
                    'mergeHeader' => true
                ],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'value' => function ($model){
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function($model){
                        return 'Info: '.$model->info.'<br>Info Boa: '.$model->info_boa;
                    },
                ],
                [
                    'attribute' => 'start_at',
                    'label' => 'Data od',
                    'format' => ['date', 'php:Y-m-d'],
                ],
                [
                    'attribute' => 'type',
                    'label' => 'Typ',
//                     'value' => 'name1'
                ],
                [
                    'attribute' => 'package',
                    'label' => 'Pakiet',
//                     'value' => ''
                ],
                
            ],
        ]);
    echo Html::endTag('div');
echo Html::endTag('div');
?>