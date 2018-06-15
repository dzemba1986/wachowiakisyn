<?php

use backend\models\DeviceType;
use common\models\User;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var DeviceTaskSearch $searchModel
 */

$this->params['breadcrumbs'][] = 'Do zrobienia';

echo $this->renderFile('@backend/views/modal/modal.php');
echo $this->renderFile('@backend/views/modal/modal_sm.php');

echo GridView::widget([
    'id' => 'task-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => 'select[name="per-page"]',
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'task-grid-pjax'
        ]    
    ],
	'summary' => 'Widoczne {count} z {totalCount}',
    'resizableColumns' => false,
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
    'columns' => [
        [
            'header' => 'Lp.',
            'class' => 'kartik\grid\SerialColumn',
            'options' => ['style'=>'width: 4%;'],
            'mergeHeader' => true
        ],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'hiddenFromExport' => FALSE,
            'value' => function() {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function($model) {
                return $model->description;
            },
        ],
        [
            'attribute' => 'create',
            'label' => 'Dzień',
            'format' => ['date', 'yyyy-MM-dd'],
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'model' => $searchModel,
                'attribute' => 'create',
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
        	'attribute' => 'create',
        	'label' => 'Godzina',	
            'value' => function ($model){
                return date("H:i", strtotime($model->create));
            },
        	'filter' => false,
        	'options' => ['style' => 'width:5%;'],
        ],
        [
        	'attribute' => 'device_type',
        	'value' => 'deviceType.name',
        	'filter' => ArrayHelper::map(DeviceType::findOrderName()->all(), 'id', 'name'),
        	'options' => ['style'=>'width:5%;'],
        ],
        [
        	'attribute' => 'device_id',
        	'value' => function ($model) {
                if ($model->device->name && $model->device->alias)
                    return $model->device->name . ' /' . $model->device->alias .  '/';
                else 
                    return $model->device->serial;
        	},
        	'filterType' => GridView::FILTER_SELECT2,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'device_id',
        		'pluginOptions' => [
        			'allowClear' => true,
        			'minimumInputLength' => 2,
        			'ajax' => [
        				'url' => Url::to(['/device/list-from-tree']),
        				'dataType' => 'json',
        				'data' => new JsExpression("function(params) {
		    				return {
		    					q : params.term,
                                type_id : [6] 
							};
						}"),
        			],
        			'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        			'templateResult' => new JsExpression('function(device) { return device.concat; }'),
        			'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
        		],
        	],
        	'filterInputOptions' => ['placeholder' => 'Kamera'],
        	'format' => 'raw',
        	'options' => ['style'=>'width:30%;']
        ],
        [
        	'attribute' => 'status',
        	'format' => 'raw',
        	'filter' => ['null' => 'W trakcie', false => 'Do wymiany'],
        	'filterOptions' => ['prompt' => ''],
        	'value' => function ($model){
        		if (is_null($model->status)) return '<span class="label label-warning">w trakcie</span>';
        		elseif (!$model->status) return '<span class="label label-danger">do wymiany</span>';
        	}
        ],
        [
        	'attribute' => 'add_user',
        	'value' => 'addUser.last_name',
        	'filterType' => GridView::FILTER_SELECT2,
        	'filter' => ArrayHelper::map(User::findOrderByLastName()->all(), 'id', 'last_name'),
        	'filterWidgetOptions' => [
        		'pluginOptions' => ['allowClear' => true],
        	],
        	'filterInputOptions' => ['placeholder' => ''],
        	'options' => ['style'=>'width:10%;']
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
        	'template' => '{close} {addcomment} {comments} {update}',
            'options' => ['style' => 'width:6%;'],
        	'buttons' => [
        		'close' => function ($model, $data) {
        			$url = Url::to(['device-task/close', 'id' => $data->id]);
        			
        			return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
        				'title' => \Yii::t('yii', 'Zamknij'),
        			    'onclick' => "
                            $('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href'));

                            return false;
                        "
        			]);
        		},
        		'addcomment' => function ($model, $data) {
        			$url = Url::to(['comment/create', 'taskId' => $data->id]);
        		
        			return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
        				'title' => \Yii::t('yii', 'Dodaj komentarz'),
        			    'onclick' => "
                            $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
        			    
                            return false;
                        "
        			]);
        		},
        		'comments' => function ($url, $model) {
        		
        			if ($model->getComments()->count() == 0){
        				return null;
        			} else {
            			$url = Url::to(['comment/index', 'taskId' => $model->id]);
            		
            			return Html::a('<span class="glyphicon glyphicon-comment"></span>', $url, [
            				'title' => \Yii::t('yii', 'Komentarze'),
            			    'onclick' => "
                                $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
            	            			    
                                return false;
                            "
            			]);
        			}
        		},
        		'update' => function($url, $model, $key) {
            		return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
            		    'title' => \Yii::t('yii', 'Edycja'),
            		    'onclick' => "
                            $('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href'));
            		    
                            return false;
                        "
            		]);
        		},
        	]
        ],     
    ],
]); 
?>