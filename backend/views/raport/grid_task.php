<?php

use backend\models\AddressShort;
use backend\models\ConnectionType;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var backend\modules\task\models\InstallTaskSearch $modelSearch
 */

$this->title = 'Zestawienia';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Montaże';

echo GridView::widget([
    'id' => 'installation-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => 'select[name="per-page"]',
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'task-grid-pjax'
        ]    
    ],
    'resizableColumns' => FALSE,
    'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
	'summary' => 'Widoczne {count} z {totalCount}',
    'showPageSummary'=>true,
	'export'=>[
    	'fontAwesome' => true,
        'showConfirmAlert' => false,
        'target' => GridView::TARGET_BLANK,
        'exportConfig' => ['pdf' => TRUE, 'json' => FALSE],
	],
    'panel' => [
    	'heading'=> '',
		'before' => $this->render('_search_task', [
				'searchModel' => $searchModel,
		]),
    ],
    'columns' => [
        [
            'header' => PageSize::widget([
                'defaultPageSize' => 100,
            	'pageSizeParam' => 'per-page',
                'sizes' => [
                    10 => 10,
                    100 => 100,
                    500 => 500,
                    1000 => 1000,
                ],
                'template' => '{list}',
        	]),
            'class'=>'kartik\grid\SerialColumn',
        	'pageSummary' => 'Łącznie',
            'options' => ['style'=>'width: 4%;'],
        ],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'value' => function ($model){
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function($model){
                return 'Info: '.$model->description;
            },
            'options' => ['style'=>'width: 4%;'],
        ],
        [	
            'attribute' => 'street',
            'value' => 'address.ulica',
            'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
            'options' => ['style' => 'width:12%;'],
        ],	
        [
            'attribute' => 'house',
            'value' => 'address.dom',
            'options' => ['style' => 'width:5%;'],
        ],
        [
            'attribute' => 'house_detail',
            'value' => 'address.dom_szczegol',
            'options' => ['style' => 'width:5%;'],
        ],
        [
            'attribute' => 'flat',
            'value' => 'address.lokal',
            'options' => ['style' => 'width:5%;'],
        ],
        [
            'attribute' => 'type_id',
            'value' => 'type.name',
            'filter' => ArrayHelper::map(ConnectionType::find()->all(), 'id', 'name'),
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute' => 'cost',
            'pageSummary'=>true,
            'options' => ['style' => 'width:5%;'],
        ],
        [
            'class'=>'kartik\grid\BooleanColumn',
            'attribute' => 'paid_psm',
            'trueLabel' => 'Tak',
            'falseLabel' => 'Nie',
            'options' => ['style'=>'width:7%;'],
        ],
        [
        	'attribute' => 'close',
            'format' => ['date', 'php:Y-m-d'],
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'close',
        		'pickerButton' => false,
        		'language'=>'pl',
        		'pluginOptions' => [
        			'format' => 'yyyy-mm-dd',
        			'todayHighlight' => true,
        		]
        	],
        	'options' => ['id'=>'start', 'style'=>'width:10%;'],
        ],
    ],
]); 
?>