<?php 
use backend\models\Teryt;
use backend\models\ConnectionType;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\models\ConnectionSearch $modelSearch
 */

echo $this->renderFile('@frontend/views/modal/modal.php');

$this->params['breadcrumbs'][] = 'Zaksięgowane';

echo GridView::widget([
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
	'resizableColumns' => FALSE,
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
	'summary' => 'Widoczne {count} z {totalCount}',
	'export' => false,
	'panel' => [
			'before' => $this->render('_search', [
					'searchModel' => $searchModel,
			]),
	],
	'rowOptions' => function($model){
		if((strtotime(date("Y-m-d")) - strtotime($model->start_date)) / (60*60*24) >= 21){
	
			return ['class' => 'after-date'];
		}
	},
	'columns' => [
	    [
	        'header' => 'Lp.',
	        'class' => 'kartik\grid\SerialColumn',
	        'options' => ['style'=>'width: 4%;'],
	        'mergeHeader' => true
	    ],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'value' => function() {

                return GridView::ROW_COLLAPSED;
            },
            'detail' => function($model){

                return 'Info: '.$model->info.'<br>Info Boa: '.$model->info_boa;
            },
        ],          
        [
        	'attribute' => 'start_date',
            'format' => ['date', 'php:Y-m-d'],
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'start_date',
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
            'attribute' => 'street',
            'value' => 'address.ulica',
            'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
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
            'class'=>'kartik\grid\BooleanColumn',
            'attribute' => 'socket',
            'header' => 'Gniazdo',
        	'trueLabel' => 'Tak',
        	'falseLabel' => 'Nie',
            'options' => ['style'=>'width:7%;'],
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
        	'attribute' => 'synch_date',
            'format' => ['date', 'php:Y-m-d'],
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'synch_date',
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
                ],
                'label' => 'Ilość',
                'template' => '{label}{list}',
                'options' => ['class' => 'form-control'],
                
            ]),
            'class' => 'kartik\grid\ActionColumn',
            'mergeHeader' => true,
            'template' => '{view}',
            'options' => ['style' => 'width:6%;'],
            'buttons' => [
                'view' => function($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                        'title' => \Yii::t('yii', 'Podgląd'),
                        'onclick' => "
                            $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
                        
                            return false;
                        "
                    ]);
                },
            ]
        ],            
    ]
]); 
?>