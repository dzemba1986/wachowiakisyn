<?php

use backend\models\DeviceType;
use common\models\User;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\ModyficationSearch $searchModel
 */

$this->params['breadcrumbs'][] = 'Zrobione';
?>
<div class="task-index">

    <?= GridView::widget([
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
                'header'=>'Lp.',
                'class'=>'yii\grid\SerialColumn',
                'options'=>['style'=>'width: 4%;'],
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'hiddenFromExport' => FALSE,
                'value' => function (){

                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function($data){

                    return 'Zgłoszenie: '.$data->description . '<br><br>Zrobiono: ' . $data->close_description;
                },
            ],  
            [
            	'attribute' => 'create',
            	'value'=> function ($model){
            		return date("Y-m-d", strtotime($model->create));
            	},
            	'filterType' => GridView::FILTER_DATE,
            	'filterWidgetOptions' => [
            		'model' => $searchModel,
            		'attribute' => 'create',
            		'pickerButton' => false,
            		'pluginOptions' => [
            			'language' => 'pl',
            			'format' => 'yyyy-mm-dd',
            			'todayHighlight' => true,
            		]
            	],
            	'options' => ['id'=>'start', 'style'=>'width:10%;'],
            ],
            [
            	'attribute' => 'device_type',
            	'value' => 'deviceType.name',
            	'filter' => ArrayHelper::map(DeviceType::findOrderName()->all(), 'id', 'name'),
            	'options' => ['style'=>'width:5%;'],
            ],
            [
            	'attribute' => 'device_id',
            	'value' => 'device.name',
            	'filterType' => GridView::FILTER_SELECT2,
            	'filterWidgetOptions' => [
            		'model' => $searchModel,
            		'attribute' => 'device_id',
            		'pluginOptions' => [
            			'allowClear' => true,
            			'minimumInputLength' => 2,
            			'ajax' => [
            				'url' => Url::to(['/device/list']),
            				'dataType' => 'json',
            				'data' => new JsExpression("function(params) {
			    				return {
			    					q : params.term,
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
//             [
//                 'attribute' => 'type_id',
//                 'value' => 'type.name',
//                 'filter' => ArrayHelper::map(TaskType::findWhereType(2)->all(), 'id', 'name'),
//                 'options' => ['style'=>'width:5%;'],
//             ],
//             [
//                 'attribute' => 'category_id',
//                 'value' => 'category.name',
//                 'filter'=> ArrayHelper::map(TaskCategory::findWhereType(2)->all(), 'id', 'name'),
//                 'options' => ['style'=>'width:5%;'],
//             ],
            [
            	'class'=>'kartik\grid\BooleanColumn',
            	'attribute'=>'status',
            	'trueLabel' => 'Tak',
            	'falseLabel' => 'Nie',
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
            	'attribute' => 'close_user',
            	'value' => 'closeUser.last_name',
            	'filterType' => GridView::FILTER_SELECT2,
            	'filter' => ArrayHelper::map(User::findOrderByLastName()->all(), 'id', 'last_name'),
            	'filterWidgetOptions' => [
            		'pluginOptions' => ['allowClear' => true],
            	],
            	'filterInputOptions' => ['placeholder' => ''],
            	'options' => ['style'=>'width:10%;']
            ],
            [
            	'attribute' => 'close',
            	'value'=> function ($model){
            		return date("Y-m-d", strtotime($model->close));
            	},
            	'filterType' => GridView::FILTER_DATE,
            	'filterWidgetOptions' => [
            		'model' => $searchModel,
            		'attribute' => 'close',
            		'pickerButton' => false,
            		//'removeButton' => false,
            		'language' => 'pl',
            		'pluginOptions' => [
            			'format' => 'yyyy-mm-dd',
            			'todayHighlight' => true,
            		]
            	],
            	'options' => ['id'=>'start', 'style'=>'width:10%;'],
            ],
            [   
                'header' => PageSize::widget([
                    'defaultPageSize' => 100,
                    'sizes' => [
                        10 => 10,
                        100 => 100,
                        500 => 500,
                        1000 => 1000,
                        5000 => 5000,
                    ],
                    'template' => '{list}',
                ]),
            ],     
        ],
    ]); ?>

</div>