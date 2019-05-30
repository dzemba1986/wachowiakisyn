<?php

use backend\modules\address\models\Teryt;
use common\models\crm\FullCalendarAsset;
use common\models\crm\TaskCategory;
use common\models\crm\TaskSubcategory;
use common\models\crm\TaskType;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\modules\task\models\InstallTaskSearch $searchModel
 */

echo $this->renderFile('@app/views/modal/modal.php');

FullCalendarAsset::register($this); ?>
<style>
.closed {
	background-color: #d7ffd7;
}

.closed + tr {
    background-color: #d7ffd7;
}

/* .table-bordered > thead > tr > th, */
.table-bordered > tbody > tr > th,
/* .table-bordered > tfoot > tr > th, */
/* .table-bordered > thead > tr > td, */
.table-bordered > tbody > tr > td {
/* .table-bordered > tfoot > tr > td { */
    border: 0px;
}

/* .colsed:nth-child(odd) { */
/* 	background-color: #d7ffd7; */
/* } */

/* .closed:nth-child(even) { */
/* 	background-color: #a6ffa6; */
/* } */

/* #connection-grid .inactiv:nth-child(odd) { */
/* 	background-color: #ebebeb; */
/* } */

/* #connection-grid .inactiv:nth-child(even) { */
/* 	background-color: #e1e1e1; */
/* } */

/* #connection-grid .sevendays { */
/* 	background-color: #FFc9cC; */
/* } */
</style>

<?php 
echo '<div id="calendar" style="padding-bottom:30px"></div>';
echo GridView::widget([
    'id' => 'task-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => 'select[name="per-page"]',
    'pjax' => true,
    'rowOptions' => function($model) {
        if ($model->status) return ['class' => 'closed'];
        elseif (is_null($model->status)) return ['class' => 'pay'];
        elseif (!$model->status) return ['class' => 'open'];
    },
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
    'columns' => [
        [
            'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['task/create']), ['id' => 'add-task']),
            'class' => 'yii\grid\SerialColumn',
//             'contentOptions' => ['rowspan' => 2],
        ],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'defaultHeaderState' => 0,
            'detailRowCssClass' => GridView::TYPE_DEFAULT,
            'width' => '40px',
            'value' => function ($model) {
                return GridView::ROW_EXPANDED;
            },
            'detail' => function ($model) {
                return $model->description;
            },
        ],
        [
            'attribute' => 'create_at',
            'format' => ['date', 'php:Y-m-d'],
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'model' => $searchModel,
                'attribute' => 'create_at',
                'pickerButton' => false,
                'language' => 'pl',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ]
            ],
        ],
        [
            'attribute' => 'street',
            'value' => 'address.ulica',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Ulica'],
            'format' => 'raw',
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
            'filter' => ArrayHelper::map(TaskType::find()->all(), 'id', 'name'),
        ],
        [
            'attribute' => 'category_id',
            'value' => 'category.name',
            'filter'=> ArrayHelper::map(TaskCategory::find()->all(), 'id', 'name'),
        ],
        [
            'attribute' => 'subcategory_id',
            'value' => 'subcategory.name',
            'filter'=> ArrayHelper::map(TaskSubcategory::find()->all(), 'id', 'name'),
        ],
        [
            'attribute' => 'close_at',
            'format' => ['date', 'php:Y-m-d'],
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'model' => $searchModel,
                'attribute' => 'close_at',
                'pickerButton' => false,
                'language' => 'pl',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ]
            ],
        ],
        [
            'class' => '\kartik\grid\CheckboxColumn',
            
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
            'class' => 'yii\grid\ActionColumn',
            'template' => '{close}',
            'buttons' => [
                'close' => function ($model, $data) {
                    $url = Url::to(['install-task/close', 'id' => $data->id]);
                    
                    return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                        'title' => \Yii::t('yii', 'Zamknij'),
                        'data-pjax' => '0',
                    ]);
                },
            ]
        ],
//                     [
//                         'attribute' => 'description',
//                         'value' => function ($model, $data) {
//                             return '<tr>Testowo</tr>';
//                         },
//                         'format' => 'raw',
//                     ],
                    //         [
                        //         	'attribute' => 'add_user',
                        //         	'value' => 'addUser.last_name',
                        //         	'filterType' => GridView::FILTER_SELECT2,
                        //         	'filter' => ArrayHelper::map(User::findOrderByLastName()->all(), 'id', 'last_name'),
                        //         	'filterWidgetOptions' => [
                            //         		'pluginOptions' => ['allowClear' => true],
                            //         	],
                            //         	'filterInputOptions' => ['placeholder' => ''],
                            //         	'format' => 'raw',
                                //         	'options' => ['style'=>'width:10%;'],
                                //             'headerOptions' => ['class' => 'skip-export'],
                        //             'contentOptions' => ['class' => 'skip-export']
                        //         ],
    ],
]);

$toDoInstallTaskUrl = Url::to(['install-task/get-to-do']);
$doneInstallTaskUrl = Url::to(['install-task/get-done']);
$toDoDeviceTaskUrl = Url::to(['device-task/get-to-do']);
$doneDeviceTaskUrl = Url::to(['device-task/get-done']);
$taskUrl = Url::to(['task/index']);
$taskCreateUrl = Url::to(['task/create']);
$js = <<<JS

$( '#calendar' ).fullCalendar({
    header: {
        right: 'agendaDay,agendaWeek today prev,next'
    },
    defaultView: 'agendaWeek',
    height: 830,
    minTime: '06:00:00',
    maxTime: '22:00:00',
    eventSources: [
    {
        url: '{$toDoInstallTaskUrl}',
        color: '#ffbf00',
        textColor: 'black',
        editable: true,
    },
//     {
//         url: '{$doneInstallTaskUrl}',
//         color: '#909090',
//         textColor: 'black',
//         editable: false,
//     },
    {
        url: '{$toDoDeviceTaskUrl}',
        color: '#ffbf00',
        textColor: 'black',
        editable: true,
    },
//     {
//         url: '{$doneDeviceTaskUrl}',
//         color: '#909090',
//         textColor: 'black',
//         editable: false,
//     }
    ],
    dayClick : function(date, jsEvent, view) {
	        	
    	$( '#modal' ).modal('show').find( '#modal-content' ).load('{$taskCreateUrl}&timestamp=' + date);
    },
});

// $( '#tasks' ).load( '{$taskUrl} #task-grid' );

JS;

$this->registerJs($js)
?>
