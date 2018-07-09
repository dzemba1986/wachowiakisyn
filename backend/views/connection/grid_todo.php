<?php 
use backend\models\AddressShort;
use backend\models\ConnectionType;
use backend\models\Package;
use kartik\grid\GridView;
use kartik\select2\Select2;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\ConnectionSearch $modelSearch
 */

echo $this->renderFile('@backend/views/modal/modal.php');
echo $this->renderFile('@backend/views/modal/modal_lg.php');

$this->params['breadcrumbs'][] = 'Do zrobienia';

// wyszukiwanie globalne
$form = ActiveForm::begin([
    'action' => ['connection/index', 'mode' => 'all'],
    'method' => 'get',
    'id' => 'global-search-form',
]); ?>

    <div class="row">
        
        <?= $form->field($searchModel, 'street', [
                'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 15px; padding-right: 3px;'], 
                'template' => "{input}\n{hint}\n{error}",
            ])->widget(Select2::className(), [
                'data' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
            	'options' => ['placeholder' => 'Ulica'],
            	'pluginOptions' => [
            		'allowClear' => true
            	],
        	])
        ?>

        <?= $form->field($searchModel, 'house', [
                'options' => ['class' => 'col-md-1', 'style' => 'padding-left: 3px; padding-right: 3px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('house')]) 
        ?>

        <?= $form->field($searchModel, 'flat', [
                'options' => ['class' => 'col-md-1', 'style' => 'padding-left: 3px; padding-right: 3px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('flat')]) 
        ?>
        
        <?= $form->field($searchModel, 'house_detail', [
                'options' => ['class' => 'col-md-1', 'style' => 'padding-left: 3px; padding-right: 3px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('house_detail')]) 
        ?>

        <?= $form->field($searchModel, 'type_id', [
                'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 3px; padding-right: 3px;'], 
                'template' => "{input}\n{hint}\n{error}",
            ])->dropDownList((ArrayHelper::map(ConnectionType::find()->all(), 'id', 'name')), ['prompt' => $searchModel->getAttributeLabel('type_id')]) 
        ?>
        
        <?= $form->field($searchModel, 'ara_id', [
                'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 3px; padding-right: 3px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('ara_id')]) 
        ?>
        
        <?= $form->field($searchModel, 'soa_id', [
                'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 3px; padding-right: 3px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('soa_id')]) 
        ?>
        
        <?= $form->field($searchModel, 'nocontract', [
	        'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 3px; padding-right: 3px;'],
	    ])->checkbox(['uncheck' => null, 'checked' => 1]) ?>  
	        
	    <?= $form->field($searchModel, 'vip', [
	        'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 3px; padding-right: 3px;'],
	    ])->checkbox(['uncheck' => null, 'checked' => 1]) ?> 
        
        <div style="padding-left: 3px; padding-right: 15px;" class="col-xs-1" ><?= Html::submitButton('Szukaj', ['class' => 'btn btn-danger', 'style' => 'width:100%']) ?></div>
        
    </div>
        
    <?php ActiveForm::end();
// wyszukiwanie globalne - koniec

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
    'export' => [
        'label' => 'PDF',
        'showConfirmAlert' => false,
        
    ],
    'exportConfig' => [
        'pdf' => ['label' => 'Wygeneruj PDF']
    ],
	'panel' => [
		'before' => '',
	],
	'rowOptions' => function($model){
    	if ($model->exec_date) {
    	    return ['class' => 'sevendays'];
    	} elseif ((strtotime(date("Y-m-d")) - strtotime($model->start_date)) / (60*60*24) >= 21){
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
            'value' => function ($model){

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
            		'endDate' => '0d'	
            	]
            ],
            'options' => ['id'=>'start', 'style'=>'width:8%;'],
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
            'options' => ['style'=>'width:7%;'],
        ],
        [
            'attribute' => 'phone_desc',
            'options' => ['style'=>'width:10%;'],
            'visible' => Yii::$app->user->id <> 19 ? false : true
        ],
        [
	        'attribute' => 'package_id',
	        'value' => 'package.name',
            'filter' => ArrayHelper::map(Package::find()->all(), 'id', 'name'),
	        'options' => ['style'=>'width:7%;'],
            'headerOptions' => ['class' => 'skip-export'],
            'contentOptions' => ['class' => 'skip-export']
        ],
        [
            'attribute' => 'wire',
            'format' => 'raw',
            'value'=> function($model, $key){
            if ($model->wire > 0){
                return '<span class="glyphicon glyphicon-ok text-success"></span>';
            } else
                return Html::a('dodaj', Url::to(['installation/create', 'connectionId' => $key]), [
                    'onclick' => "
                        $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
                    
                        return false;
                    "
                ]);
            },
            'filter' => [1 => 'Tak', 0 => 'Nie'],
            'options' => ['style'=>'width:8%;'],
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
        	'attribute' => 'task_id',
        	'label' => 'Montaż',
        	'format' => 'raw',	
        	'value'=> function($model, $key){
        		if (!is_null($model->task_id)){
        			if (is_object($model->task))
        				return Html::a(date('Y-m-d', strtotime($model->task->start)), Url::to(['task/install-task/view-calendar', 'connectionId' => $key]), [
        				    'onclick' => "
                                $('#modal-lg').modal('show').find('#modal-lg-content').load($(this).attr('href'));
            				    
                                return false;
                            "
        				]);
        		}
        		elseif ($model->socket > 0)
        			return null;
        		else
        			return Html::a('dodaj', Url::to(['task/install-task/view-calendar', 'connectionId' => $key]), [
        			    'title' => \Yii::t('yii', 'Edycja'),
        			    'data-toggle' => 'modal',
        			    'onclick' => "
                            $('#modal-lg').modal('show').find('#modal-lg-content').load($(this).attr('href'));
   
                            return false;
                        "
        			]);
			},
			'filter' => false,
			'options' => ['style'=>'width:8%;'],
        ],
        [
        	'attribute' => 'conf_date',
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
			'options' => ['style'=>'width:8%;'],
            'headerOptions' => ['class' => 'skip-export'],
            'contentOptions' => ['class' => 'skip-export'],
            'visible' => Yii::$app->user->id <> 19 ? true : false
		],
		[
		    'attribute' => 'pay_date',
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
		    'options' => ['style'=>'width:8%;'],
		    'headerOptions' => ['class' => 'skip-export'],
		    'contentOptions' => ['class' => 'skip-export'],
		    'visible' => Yii::$app->user->id <> 19 ? true : false
		],
        [   
            'header' => PageSize::widget([
                'defaultPageSize' => 100,
                'pageSizeParam' => 'per-page',
                'sizes' => [
                    10 => 10,
                    25 => 25,
                    50 => 50,
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
            'template' => '{view} {update} {tree} {history}',
            //'options' => ['style' => 'width:6%;'],
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
        			if($model->canConfigure()){
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
            		$url = Url::to(['history/history-by-connection', 'id' => $key]);
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