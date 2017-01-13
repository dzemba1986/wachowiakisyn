<?php

use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

    <?= GridView::widget([
        'id' => 'ip-grid',
        'dataProvider' => $dataProvider,
        //'filterModel' => $modelSubnet,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'ip-grid-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
        //'showPageSummary' => TRUE,
    	'export' => false,
        'columns' => [
            'ip',		
//         	'subnet',
        	[
		        'attribute' => 'device',
		        'value' => 'modelDevice.name',
        	],
        	[
        		'label' => 'Typ',
        		'value' => 'modelDevice.modelType.name',
        	],
        	[
        		'class' => 'kartik\grid\BooleanColumn',
        		'attribute' => 'main',
        		'trueLabel' => 'Tak',
        		'falseLabel' => 'Nie',
        	],
//         	[
//         		'class' => 'yii\grid\ActionColumn',
// //         		'template' => '{update}{tree}{delete}',
//         		'buttons' => [
//         			'view' => function ($model, $data) {
//         				$url = Url::toRoute(['subnet/index', 'vlan' => $data->id]);
//         				return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
//         					'title' => \Yii::t('yii', 'Widok'),
//         					'data-pjax' => '0',
//         				]);
//         			},
//         		]
//         	],
        ]                
    ]); ?>