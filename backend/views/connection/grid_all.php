<?php 
use backend\models\AddressShort;
use backend\models\ConnectionType;
use backend\models\Package;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var backend\models\ConnectionSearch $modelSearch
 */

$this->params['breadcrumbs'][] = 'Wszystkie';
echo $this->renderFile('@backend/views/modal/modal.php');

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
	'resizableColumns' => false,
	'export' => false,
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => ''
    ],
	'summary' => 'Widoczne {count} z {totalCount}',
	'panel' => [
			'before' => $this->renderAjax('_search', [
					'searchModel' => $searchModel,
			]),
	],
	'rowOptions' => function($model){
    	if ($model->exec_date) {
    	    return ['class' => 'sevendays'];
    	} elseif ($model->pay_date <> null && $model->close_date == null) {
			return ['class' => 'pay'];
		} elseif ($model->close_date <> null) {
			return ['class' => 'inactiv'];
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
            'value' => function ($model, $key, $index, $column){

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
            'attribute' =>'type_id',
            'value' => 'type.name',
            'filter'=> ArrayHelper::map(ConnectionType::find()->all(), 'id', 'name'),
            'options' => ['style'=>'width:5%;'],
        ],
        [
	        'attribute' => 'package_id',
	        'value' => 'package.name',
            'filter'=> ArrayHelper::map(Package::find()->all(), 'id', 'name'),
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
            'format' => ['date', 'php:Y-m-d'],
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
                ],
                'label' => 'Ilość',
                'template' => '{label}{list}',
                'options' => ['class' => 'form-control'],
                
            ]),
            'class' => 'kartik\grid\ActionColumn',
            'mergeHeader' => true,
            'template' => '{view} {update} {history} {tree}',
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
        	    'update' => function($url, $model, $key) {
            	    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
            	        'title' => \Yii::t('yii', 'Edycja'),
            	        'onclick' => "
                            $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
            	        
                            return false;
                        "
            	    ]);
        	    },
        	    'tree' => function ($url, $model, $key) {
            	    if($model->canConfigure()) {
            	        $url = Url::to(['host/add-on-tree', 'connectionId' => $key]);
            	        return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
            	            'title' => \Yii::t('yii', 'Konfiguracja'),
            	            'onclick' => "
                                $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
            	            
                                return false;
                            "
            	        ]);
            	    } elseif ($model->host_id) {
            	        $url = Url::to(['tree/index', 'id' => $model->host_id . '.0']);
            	        return Html::a('<span class="glyphicon glyphicon-play"></span>', $url, [
            	            'title' => \Yii::t('yii', 'SEU'),
            	            'target'=>'_blank',
            	        ]);
            	    } else
            	        return null;
        	    },
        		'history' => function ($url, $model, $key) {
                    $url = Url::to(['connection/history', 'id' => $key]);
                    return Html::a('<span class="glyphicon glyphicon-menu-hamburger"></span>', $url, [
                        'title' => \Yii::t('yii', 'Historia'),
                        'onclick' => "
                            $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
    
                            return false;
                        "
                    ]);
        		}
        	]
        ],            
    ]
]);
?>