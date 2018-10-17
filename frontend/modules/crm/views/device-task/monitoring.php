<?php

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

echo $this->renderFile('@frontend/views/modal/modal.php');

$this->params['breadcrumbs'][] = 'CRM';
$this->params['breadcrumbs'][] = 'Zgłoszenia';

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
    'resizableColumns' => FALSE,
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
    'columns' => [
        [
            'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['device-task/create']), [
                'onclick' => "
                    $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));

                    return false;
                "
            ]),
            'class' => 'yii\grid\SerialColumn',
            'options' => ['style'=>'width: 4%;'],
        ],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'hiddenFromExport' => FALSE,
            'value' => function (){
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model){
                return $model->description;
            },
        ],
        [
            'attribute' => 'create',
            'label' => 'Dzień',
            'format' => ['date', 'php:Y-m-d'],
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
        	'attribute' => 'device_id',
        	'value' => 'device.alias',
        	'filterType' => GridView::FILTER_SELECT2,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'device_id',	
        		'pluginOptions' => [
        			'allowClear' => true,
        			'minimumInputLength' => 2,
        			'ajax' => [
        				'url' => Url::to(['/seu/camera/list']),
        				'dataType' => 'json',
        				'data' => new JsExpression("function(params) {
		    				return {
		    					q : params.term,
							};
						}"),
        			],
        			'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        			'templateResult' => new JsExpression('function(results) { return results.alias; }'),
        			'templateSelection' => new JsExpression('function (results) { return results.alias; }'),
        		],
        	],
        	'filterInputOptions' => ['placeholder' => 'Kamera'],
        	'format' => 'raw',
        	'options' => ['style'=>'width:30%;']
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
        	'format' => 'raw',
        	'options' => ['style'=>'width:10%;']
        ],
        [
        	'attribute' => 'status',
        	'format' => 'raw',
        	'filter' => ['null' => 'W trakcie', false => 'Do wymiany'],	
        	'filterOptions' => ['prompt' => ''],
        	'value' => function ($model){
        		if ($model->status) return '<span class="label label-success">zrobione</span>';
        		elseif (is_null($model->status)) return '<span class="label label-warning">w trakcie</span>';
        		else return '<span class="label label-danger">do wymiany</span>';
        	}
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
            'template' => '{comments}',
            'options' => ['style' => 'width:6%;'],
            'buttons' => [
                'comments' => function($url, $model) {
                
                    $url = Url::to(['comment/index', 'taskId' => $model->id]);
                    
                    return Html::a('<span class="glyphicon glyphicon-comment"></span>', $url, [
                        'title' => \Yii::t('yii', 'Komentarze'),
                        'onclick' => "
                                $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
                        
                                return false;
                            "
                    ]);
                },
            ]
        ],
    ],
]); 
?>