<?php

use common\models\User;
use common\models\seu\devices\Camera;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\crm\DeviceTask;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var DeviceTaskSearch $searchModel
 */
$this->params['breadcrumbs'][] = 'CRM';
$this->params['breadcrumbs'][] = 'Do zrobienia';

echo $this->renderFile('@app/views/modal/modal.php');
echo $this->renderFile('@app/views/modal/modal_sm.php');

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
                return $model->desc;
            },
        ],
        [
            'attribute' => 'create_at',
            'label' => 'Data utworzenia',
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
        	'attribute' => 'create_at',
        	'label' => 'Godzina',
            'format' => ['date', 'php:H:i'],
//             'value' => function ($model){
//                 return date("H:i", strtotime($model['create']));
//             },
        	'filter' => false,
        	'options' => ['style' => 'width:5%;'],
        ],
        [
        	'attribute' => 'device_id',
            'label' => 'Kamera',
//             'value' => 'device.name',
            'value' => function ($model, $key, $index, $column) {
                if ($model->device->name && $model->device->alias)
                    return Html::a($model->device->name . ' [ ' . $model->device->alias .  ' ]', ['/seu/link/index', 'id' => $key], ['target'=>'_blank']);
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
        		        'url' => Url::to(['/seu/devices/device/list-from-tree']),
        		        'dataType' => 'json',
        		        'data' => new JsExpression("function(params) {
        					return {
        						q : params.term,
                                type : " . json_encode([Camera::TYPE]) . "
    						};
					   }")
        		    ],
        			'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        			'templateResult' => new JsExpression('function(device) { return device.concat; }'),
        			'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
        		],
        	],
        	'filterInputOptions' => ['placeholder' => 'Kamera'],
        	'format' => 'raw',
        	'options' => ['style'=>'width:25%;']
        ],
        [
        	'attribute' => 'status',
        	'format' => 'raw',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => DeviceTask::$statusName,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear'=>true],
                'options' => ['multiple'=>true],
            ],
        	'filterOptions' => ['prompt' => ''],
        	'value' => function ($model) {
        		if ($model->status == 0) return '<span class="label label-success">'. DeviceTask::$statusName[$model->status].'</span>';
        		if ($model->status == 1) return '<span class="label label-danger">'. DeviceTask::$statusName[$model->status].'</span>';
        		if ($model->status == 2) return '<span class="label label-info">'. DeviceTask::$statusName[$model->status].'</span>';
        	},
        	'options' => ['style'=>'width:15%;']
        ],
        [
            'attribute' => 'category_id',
            'format' => 'raw',
            'filter' => DeviceTask::$categoryName,
            'filterOptions' => ['prompt' => ''],
            'value' => function ($model) {
            if ($model->category_id <> 2) return '<span class="label label-warning">' . DeviceTask::$categoryName[$model->category_id].'</span>';
            else return '<span class="label label-danger">' . DeviceTask::$categoryName[$model->category_id].'</span>';
            }
        ],
        [
            'attribute' => 'close_by',
            'value' => 'closeBy.last_name',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(User::findOrderByLastName()->all(), 'id', 'last_name'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => ''],
            'options' => ['style'=>'width:7%;']
        ],
        [
            'attribute' => 'close_at',
            'label' => 'Data zamknięcia',
            'format' => ['date', 'php:Y-m-d H:i'],
            'filter' => false,
            'options' => ['style' => 'width:10%;'],
        ],
        [
            'class'=>'kartik\grid\BooleanColumn',
            'attribute' => 'fulfit',
            'trueLabel' => 'Tak',
            'falseLabel' => 'Nie',
            'options' => ['style'=>'width:5%;'],
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
        	'template' => '{close} {addcomment} {comments} {update} {tree}',
            'visibleButtons' => [
                'close' => function ($model) { return $model->status != 1; },
                'comments' => function ($model) { return $model->comments_count; },
                'update' => function ($model) { return $model->status != 1; },
            ],
            'options' => ['style' => 'width:6%;'],
        	'buttons' => [
        		'close' => function ($url, $model, $key) {
        			$url = Url::to(['device-task/close', 'id' => $model->id]);
        			
        			return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
        				'title' => \Yii::t('yii', 'Zamknij'),
        			    'onclick' => "
                            $('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href'));

                            return false;
                        "
        			]);
        		},
        		'addcomment' => function ($url, $model, $key) {
        			$url = Url::to(['comment/create', 'taskId' => $key]);
        		
        			return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
        				'title' => \Yii::t('yii', 'Dodaj komentarz'),
        			    'onclick' => "
                            $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
        			    
                            return false;
                        "
        			]);
        		},
        		'comments' => function($url, $model, $key) {
        		
        			$url = Url::to(['comment/index', 'taskId' => $key]);
        			return Html::a('<span class="glyphicon glyphicon-comment"></span>', $url, [
        				'title' => \Yii::t('yii', 'Komentarze'),
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