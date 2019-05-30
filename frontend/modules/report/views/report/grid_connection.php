<?php 
use backend\modules\address\models\Teryt;
use common\models\soa\ConnectionType;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;

/**
 * @var View $this
 * @var ConnectionSearch $searchModel
 * @var ActiveForm $form 
 */

$this->title = 'Zestawienia';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Podłączenia';
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
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
	'summary' => 'Widoczne {count} z {totalCount}',
	'resizableColumns' => FALSE,
	'export'=>[
    	'fontAwesome' => true,
        'showConfirmAlert' => false,
        'target'=>GridView::TARGET_BLANK,
        'exportConfig' => ['pdf' => TRUE, 'json' => FALSE],
    ],
	'panel' => [
		'heading'=> '',
		'before' => $this->render('_search_connection', [
			'searchModel' => $searchModel,
		]),
	],
	'rowOptions' => function($model){
		if((strtotime(date("Y-m-d")) - strtotime($model->start_date)) / (60*60*24) >= 21){
	
			return ['class' => 'afterdate'];
		}
		elseif ($model->pay_date <> null) {
	
			return ['class' => 'activ'];
		}
		elseif ($model->close_date <> null) {
	
			return ['class' => 'inactiv'];
		}
	},
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
			'class'=>'yii\grid\SerialColumn',
           	//'options'=>['style'=>'width: 5%;'],
		],        
		[
			'attribute' => 'start_date',
		    'format' => ['date', 'php:Y-m-d'],
			'filterType' => GridView::FILTER_DATE,
			'filterWidgetOptions' => [
				'model' => $searchModel,
				'attribute' => 'start_date',
				'pickerButton' => false,
				'language'=>'pl',
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
            'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
        ],	
        [
            'attribute' => 'house',
            'value' => 'address.dom',
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute' => 'house_detail',
            'value' => 'address.dom_szczegol',
        ],
        [
            'attribute' => 'flat',
            'value' => 'address.lokal',
        ],
        [
            'attribute' => 'type_id',
            'value' => 'type.name',
            'filter'=> ArrayHelper::map(ConnectionType::find()->all(), 'id', 'name'),
        ],
		[
        	'class' => 'kartik\grid\BooleanColumn',
            'attribute' => 'again',
            'trueLabel' => 'Tak',
            'falseLabel' => 'Nie',
        ],
        [
            'class' => 'kartik\grid\BooleanColumn',
            'attribute' => 'socket',
            'header' => 'Gniazdo',
        	'trueLabel' => 'Tak',
        	'falseLabel' => 'Nie',
            //'options' => ['style'=>'width:7%;'],
        ],                       
		[
			'attribute' => 'conf_date',
			'value'=> 'conf_date',
			'filterType' => GridView::FILTER_DATE,
			'filterWidgetOptions' => [
				'model' => $searchModel,
				'attribute' => 'conf_date',
				'pickerButton' => false,
				'language'=>'pl',
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
				'language'=>'pl',
				'pluginOptions' => [
					'format' => 'yyyy-mm-dd',
					'todayHighlight' => true,
					'endDate' => '0d',
				]
			],
			'options' => ['id'=>'start', 'style'=>'width:10%;'],
		],
    ]
]); 
?>