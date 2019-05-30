<?php 

/**
 * @var yii\web\View $this
 * @var backend\models\ConnectionSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use backend\modules\address\models\Teryt;
use common\models\soa\ConnectionType;
use common\models\soa\Package;
use kartik\grid\GridView;
use kartik\select2\Select2;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

echo $this->renderFile('@app/views/modal/modal.php');

$this->params['breadcrumbs'][] = 'SOA';
$this->params['breadcrumbs'][] = 'Wszystkie';

// wyszukiwanie globalne
$form = ActiveForm::begin([
    'action' => ['index', 'mode' => 'all'],
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
	'resizableColumns' => false,
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => ''
    ],
	'summary' => 'Widoczne {count} z {totalCount}',
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
            	        $url = $model->addHostUrl;
            	        return Html::a('<span class="glyphicon glyphicon-plus"></span>', $model->addHostUrl, [
            	            'title' => \Yii::t('yii', 'Konfiguracja'),
            	            'onclick' => "
                                $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
            	            
                                return false;
                            "
            	        ]);
            	    } elseif ($model->host_id) {
            	        return Html::a('<span class="glyphicon glyphicon-play"></span>', $model->treeUrl, [
            	            'title' => \Yii::t('yii', 'SEU'),
            	            'target'=>'_blank',
            	        ]);
            	    } else
            	        return null;
        	    },
        		'history' => function ($url, $model, $key) {
                    $url = Url::to(['/history/history/connection', 'id' => $key]);
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