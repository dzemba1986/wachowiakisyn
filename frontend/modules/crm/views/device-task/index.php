<?php

use common\models\crm\Task;
use common\models\seu\devices\Camera;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var DeviceTaskSearch $searchModel
 */
$this->params['breadcrumbs'][] = 'CRM';
$this->params['breadcrumbs'][] = 'Zgłoszenia kamer';

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
// 	'formatter' => [
// 		'class' => 'yii\i18n\Formatter',
// 	    'defaultTimeZone' => 'Europe/Warsaw',
// 		'nullDisplay' => ''
// 	],
    'columns' => [
        [
            'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', ['create-camera-task'], [
                'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
            ]),
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
//                 return date("H:i", strtotime($model->create_at));
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
                    return 'SN: ' . $model->device->serial;
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
            'attribute' => 'category_id',
            'value' => 'category.name',
            //             'filter' => ArrayHelper::map(TaskCategory::find()->where(['parent_id' => 0])->asArray()->all(), 'id', 'name'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterOptions' => ['prompt' => ''],
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
                'options' => ['multiple' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Kategoria'],
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->status == 0) return '<span class="label label-success">'. Task::STATUS[$model->status].'</span>';
                if ($model->status == 1) return '<span class="label label-danger">'. Task::STATUS[$model->status].'</span>';
                if ($model->status == 2) return '<span class="label label-info">'. Task::STATUS[$model->status].'</span>';
            },
            'filter' => Task::STATUS,
            'filterType' => GridView::FILTER_SELECT2,
            'filterOptions' => ['prompt' => ''],
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
                'options' => ['multiple' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Status'],
        ],
        [
            'attribute' => 'comments_count',
            'header' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-comment']),
            'format' => 'raw',
            'value' => function ($model, $key) {
                if ($model->comments_count > 0) return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-comment']), ['comment/index', 'taskId' => $key], [
        	        'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
                ]);
            },
        	'options' => ['style' => 'width:2%;'],
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
            'class' => ActionColumn::class,
            'mergeHeader' => true,
        	'template' => '{view} {update} {addcomment} {close}',
            'dropdown' => true,
            'dropdownMenu' => ['style' => 'left: -100px;'],
            'dropdownButton' => ['label' => 'Akcje','class'=>'btn btn-secondary', 'style' => 'padding: 0px 5px'],
            'visibleButtons' => [
                'close' => function ($model) { return $model->status != 1; },
                'update' => function ($model) { return $model->status != 1; },
            ],
            'options' => ['style' => 'width:6%;'],
        	'buttons' => [
        	    'view' => function ($url, $model, $key) {
            	    $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span> Podgląd', [$model::CONTROLLER . '/view', 'id' => $key], [
            	        'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
            	    ]);
            	    return Html::tag('li', $link, []);
        	    },
        	    'update' => function ($url, $model, $key) {
            	    $link = Html::a('<span class="glyphicon glyphicon-pencil"></span> Edycja', [$model::CONTROLLER . '/update', 'id' => $key], [
            	        'onclick' => "$('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href')); return false;"
            	    ]);
            	    return Html::tag('li', $link, []);
        	    },
        	    'addcomment' => function ($url, $model, $key) {
            	    $link = Html::a('<span class="glyphicon glyphicon-plus"></span> Dodaj komentarz', ['comment/create', 'taskId' => $key], [
            	        'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
            	    ]);
            	    return Html::tag('li', $link, []);
        	    },
        	    'close' => function ($url, $model, $key) {
            	    $link = Html::a('<span class="glyphicon glyphicon-ok"></span> Zamknij', [$model::CONTROLLER . '/close', 'id' => $key], [
            	        'onclick' => "$('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href')); return false;"
            	    ]);
            	    return Html::tag('li', $link, []);
        	    },
        	]
        ],     
    ],
]); 
?>