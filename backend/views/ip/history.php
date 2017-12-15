<?php

use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Historia IP';
$this->params['breadcrumbs'][] = $this->title;

?>

    <?= GridView::widget([
        'id' => 'ip-history-grid',
        'dataProvider' => $dataProvider,
    	'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'ip-history-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
        //'showPageSummary' => TRUE,
    	'export' => false,
        'columns' => [
            'ip',		
        	[
        		'attribute' => 'from_date',
        		'format' => ['date', 'php:Y-m-d H:i:s']
			],        			
        	[
        		'attribute' => 'to_date',
        		'format' => ['date', 'php:Y-m-d H:i:s']
        	],  
        	[
        		'attribute' => 'address',
        		'value' => function ($model) {
        		    return $model->modelAddress->toString();
        		},
        	]
        ]                
    ]); ?>