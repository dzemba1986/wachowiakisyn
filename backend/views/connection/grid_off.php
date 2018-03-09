<?php 
use app\models\Package;
use backend\models\AddressShort;
use backend\models\ConnectionType;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var backend\models\ConnectionSearch $modelSearch
 */

$this->params['breadcrumbs'][] = 'Odłączone';
?>

<?= GridView::widget([
	'id' => 'connection-grid',
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'filterSelector' => 'select[name="per-page"]',
	'pjax' => true,
	'pjaxSettings' => [
		'options' => [
			'id' => 'connection-grid-pjax'
		]
	],
	'resizableColumns' => false,
	'summary' => 'Widoczne {count} z {totalCount}',
	'export' => false,
	'panel' => [
		'before' => $this->renderAjax('_search', [
			'searchModel' => $searchModel,
		]),
	],
	'rowOptions' => function($model){	
		return ['class' => 'inactiv'];
	},
	'columns' => [
        [
			'header' => 'Lp.',
			'class' => 'kartik\grid\SerialColumn',
           	'options' => ['style'=>'width: 4%;'],
            'mergeHeader' => true,
		],        
		[
			'attribute' => 'start_date',
		    'value'=> function($model) { return date('Y-m-d', strtotime($model->start_date)); },
			'filterType' => GridView::FILTER_DATE,
			'filterWidgetOptions' => [
				'model' => $searchModel,
				'attribute' => 'start_date',
				'pickerButton' => false,
				'language' => 'pl',
				'pluginOptions' => [
					'format' => 'yyyy-mm-dd',
					'todayHighlight' => true,
					'endDate' => '0d'
				]
			],
			'options' => ['id'=>'start', 'style'=>'width:10%;'],
		],
        [	
            'attribute' => 'street',
            'value' => 'address.ulica',
            'filter'=> ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
            'options' => ['style'=>'width:12%;'],
        ],	
        [
            'attribute' => 'house',
            'value' => 'address.dom',
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute' => 'house_detail',
            'value' => 'address.dom_szczegol',
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute' => 'flat',
            'value' => 'address.lokal',
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute' => 'type_id',
            'value' => 'type.name',
            'filter' => ArrayHelper::map(ConnectionType::find()->all(), 'id', 'name'),
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute' => 'package_id',
            'value' => 'package.name',
            'filter' => ArrayHelper::map(Package::find()->all(), 'id', 'name'),
            'options' => ['style'=>'width:5%;'],
        ],
        [
        	'class' => 'kartik\grid\BooleanColumn',
        	'header' => 'Umowa',
        	'attribute' => 'nocontract',
        	'trueLabel' => 'Nie',
        	'falseLabel' => 'Tak',
        	'trueIcon' => GridView::ICON_INACTIVE,
        	'falseIcon' => GridView::ICON_ACTIVE,
        	'options' => ['style'=>'width:5%;'],
        ],
        [
            'class' => 'kartik\grid\BooleanColumn',
            'attribute' => 'socket',
            'header' => 'Gniazdo',
            'trueLabel' => 'Tak',
            'falseLabel' => 'Nie',
            'options' => ['style'=>'width:7%;'],
        ],
        [
        	'attribute' => 'conf_date',
        	'value'=> 'conf_date',
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'conf_date',
        		'pickerButton' => false,
        		'language' => 'pl',
        		'pluginOptions' => [
        			'format' => 'yyyy-mm-dd',
        			'todayHighlight' => true,
        			'endDate' => '0d',
        		]
        	],
        	'options' => ['id'=>'start', 'style'=>'width:10%;'],
        ],
        [
        	'attribute' => 'pay_date',
        	'value'=> 'pay_date',
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'pay_date',
        		'pickerButton' => false,
        		'language' => 'pl',
        		'pluginOptions' => [
        			'format' => 'yyyy-mm-dd',
        			'todayHighlight' => true,
        			'endDate' => '0d',
        		]
        	],
        	'options' => ['id'=>'start', 'style'=>'width:10%;'],
        ],
        [
        	'attribute' => 'close_date',
            'value'=> function($model) { return date('Y-m-d', strtotime($model->close_date)); },
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'close_date',
        		'pickerButton' => false,
        		'language' => 'pl',
        		'pluginOptions' => [
        			'format' => 'yyyy-mm-dd',
        			'todayHighlight' => true,
        			'endDate' => '0d',
        		]
        	],
        	'options' => ['id'=>'start', 'style'=>'width:10%;'],
        ],
        [   
            'header' => PageSize::widget([
                'defaultPageSize' => 100,
                'pageSizeParam' => 'per-page',
                'sizes' => [
                    10 => 10,
                    100 => 100,
                    500 => 500,
                    1000 => 1000,
                    2000 => 2000,
                ],
                'label' => 'Ilość',
                'template' => '{label}{list}',
                'options' => ['class'=>'form-control'],
            ]),
            'class' => 'kartik\grid\ActionColumn',
            'mergeHeader' => true,
            'template' => '{view} {update}',
            'options' => ['style' => 'width:6%;'],
        ],            
    ]
]); 
?>