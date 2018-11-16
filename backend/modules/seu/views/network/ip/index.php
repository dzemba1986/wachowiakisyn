<?php

/**
 * @var \yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use kartik\grid\GridView;

echo GridView::widget([
    'id' => 'ip-grid',
    'dataProvider' => $dataProvider,
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'ip-grid-pjax'
        ]    
    ],
    'resizableColumns' => false,
    'summary' => 'Widoczne {count} z {totalCount}',
    'columns' => [
        'ip',		
    	[
    	    'label' => 'Nazwa',
	        'attribute' => 'device',
	        'value' => 'device.name',
    	],
    	[
    		'label' => 'Typ',
    		'value' => 'device.type.name',
    	],
    	[
    		'class' => 'kartik\grid\BooleanColumn',
    		'attribute' => 'main',
    	],
    ]                
]); 
?>