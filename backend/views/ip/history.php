<?php

use kartik\grid\GridView;

/**
 * @var \yii\web\View $title
 * @var backend\models\HistoryIp $historyIp
 */

$this->title = 'Historia IP';
$this->params['breadcrumbs'][] = $this->title;

?>

    <?= GridView::widget([
        'id' => 'ip-history-grid',
        'dataProvider' => $dataProvider,
    	'filterModel' => $historyIp,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'ip-history-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
    	'export' => false,
        'columns' => [
            'ip',
            [
                'attribute' => 'address',
                'value' => function ($model) {
                    return $model->address->toString();
                }
            ],
        	[
        		'attribute' => 'from_date',
        		'format' => ['date', 'php:Y-m-d H:i:s']
			],        			
        	[
        		'attribute' => 'to_date',
        		'format' => ['date', 'php:Y-m-d H:i:s']
        	]  
        ]                
    ]); ?>