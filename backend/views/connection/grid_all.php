<?php 
use app\models\Package;
use backend\models\Address;
use backend\models\Device;
use backend\models\Type;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = 'Wszystkie';
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
	'resizableColumns' => FALSE,
	//'showPageSummary' => TRUE,
	'export' => false,
	'summary' => 'Widoczne {count} z {totalCount}',
	'panel' => [
			'before' => $this->render('_search', [
					'searchModel' => $searchModel,
			]),
	],
	'rowOptions' => function($model){
		if ($model->pay_date <> null && $model->close_date == null) {
	
			return ['class' => 'pay'];
		}
		elseif ($model->close_date <> null) {
	
			return ['class' => 'inactiv'];
		}
	},
	'columns' => [
        [
			'header'=>'Lp.',
			'class'=>'yii\grid\SerialColumn',
           	'options'=>['style'=>'width: 4%;'],
		],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'value' => function ($model, $key, $index, $column){

                return GridView::ROW_COLLAPSED;
            },
            'detail' => function($data){

                return 'Info: '.$data->info.'<br>Info Boa: '.$data->info_boa;
            },
        ], 
        [
        	'attribute' => 'start_date',
        	'value'=> 'start_date',
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'start_date',
        		'pickerButton' => false,
        		'pluginOptions' => [
        			'language' => 'pl',
        			'format' => 'yyyy-mm-dd',
        			'todayHighlight' => true,
        			'endDate' => '0d',
        		]
        	],
        	'options' => ['id'=>'start', 'style'=>'width:10%;'],
        ],
        [	
            'attribute'=>'street',
            'value'=>'modelAddress.ulica',
            'filter'=> Html::activeDropDownList($searchModel, 'street', ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->orderBy('ulica')->all(), 'ulica', 'ulica'), ['prompt'=>'', 'class'=>'form-control']),
            'options' => ['style'=>'width:12%;'],
        ],	
        [
            'attribute'=>'house',
            'value'=>'modelAddress.dom',
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute'=>'house_detail',
            'value'=>'modelAddress.dom_szczegol',
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute'=>'flat',
            'value'=>'modelAddress.lokal',
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute'=>'type',
            'value'=>'modelType.name',
            'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(Type::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
            'options' => ['style'=>'width:5%;'],
        ],
        [
	        'attribute'=>'package',
	        'value'=>'modelPackage.name',
	        'filter'=> Html::activeDropDownList($searchModel, 'package', ArrayHelper::map(Package::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
	        'options' => ['style'=>'width:5%;'],
        ],
        [
	        'class'=>'kartik\grid\BooleanColumn',
	        'header'=>'Umowa',
	        'attribute'=>'nocontract',
	        'trueLabel' => 'Nie',
	        'falseLabel' => 'Tak',
	        'trueIcon' => GridView::ICON_INACTIVE,
	        'falseIcon' => GridView::ICON_ACTIVE,
	        'options' => ['style'=>'width:5%;'],
        ],
        [
            'class'=>'kartik\grid\BooleanColumn',
            'attribute' => 'socket',
            'trueLabel' => 'Tak',
	        'falseLabel' => 'Nie',
            //'value' => 'socket',
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
        		'pluginOptions' => [
        			'language' => 'pl',
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
        		'pluginOptions' => [
        			'language' => 'pl',
        			'format' => 'yyyy-mm-dd',
        			'todayHighlight' => true,
        			'endDate' => '0d',
        		]
        	],
        	'options' => ['id'=>'start', 'style'=>'width:10%;'],
        ],
        [
        	'attribute' => 'close_date',
        	'value'=> 'close_date',
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'close_date',
        		'pickerButton' => false,
        		'pluginOptions' => [
        			'language' => 'pl',
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
                'template' => '{list}',
            ]),
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {tree}',
        	'buttons' => [
        		'tree' => function ($model, $data) {
        			if($data->mac && $data->port >= 0 && $data->device && !$data->nocontract && !$data->host && $data->wire > 0 && is_null($data->close_date)){
        				$url = Url::toRoute(['tree/add', 'id' => $data->id, 'host' => true]);
        				return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
        					'title' => \Yii::t('yii', 'Zamontuj'),
        					'data-pjax' => '0',
        				]);
        			} elseif($data->host){
        				$url = Url::toRoute(['tree/index', 'id' => $data->host . '.0']);
        				return Html::a('<span class="glyphicon glyphicon-play"></span>', $url, [
        					'title' => \Yii::t('yii', 'SEU'),
        					'data-pjax' => '0',
        				]);
        			} else
        				return null;
        		},
        	]
        ],            
    ]
		]); 
?>