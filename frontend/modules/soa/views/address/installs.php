<?php 

use kartik\grid\GridView;
use yii\helpers\Html;

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'resizableColumns' => false,
            'export' => false,
            'pjax' => false,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => 'Lp.',
                ],
                [
                    'attribute' => 'name',
                    'label' => 'Typ',
                ],
                [
                    "attribute" => "count",
                    'label' => 'Ilość',
                ]
                
            ]
        ]);
    echo Html::endTag('div');
echo Html::endTag('div');
?>