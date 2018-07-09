<?php

use kartik\grid\GridView;

?>

    <?= GridView::widget([
        'id' => 'ip-grid',
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'ip-grid-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
    	'export' => false,
        'columns' => [
            'ip',		
        	[
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
        		'trueLabel' => 'Tak',
        		'falseLabel' => 'Nie',
        	],
        ]                
    ]); ?>