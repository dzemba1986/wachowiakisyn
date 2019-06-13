<?php

use backend\modules\address\models\Teryt;
use common\models\User;
use common\models\crm\InstallTask;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\modules\task\models\InstallTaskSearch $searchModel
 */

$this->params['breadcrumbs'][] = 'CRM';
$this->params['breadcrumbs'][] = 'Montaże';

require_once '_modal_calendar.php';
require_once '_modal_task.php';
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
                'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['install-task/view-calendar']), ['class' => 'add-task']),
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
                    return $model->desc;
                },
            ],
            [
                'attribute' => 'start_at',
                'label' => 'Dzień',
                'format' => ['date', 'yyyy-MM-dd'],
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                	'model' => $searchModel,
                	'attribute' => 'start',
                	'pickerButton' => false,
                	'language' => 'pl',
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		'todayHighlight' => true,
                	]
                ],
                'options' => ['id'=>'start', 'style'=>'width:10%;'],
            ],
            [
            	'attribute' => 'start_at',
            	'label' => 'Od',
            	'filter' => false,
                'format' => ['date', 'php:H:i'],
//             	'value' => function ($model){
//             		return date("H:i", strtotime($model->start_at));
//             	},	
                'options' => ['id'=>'start', 'style'=>'width:5%;']
			],
			[
				'attribute' => 'end_at',
				'label' => 'Do',
				'filter' => false,
                'format' => ['date', 'php:H:i'],
// 				'value' => function ($model){
// 					return date("H:i", strtotime($model->end));
//             	},
            	'options' => ['id'=>'start', 'style'=>'width:5%;']
            ],
            [
            	'attribute' => 'street',
            	'value' => 'address.ulica',
            	'filterType' => GridView::FILTER_SELECT2,
            	'filter' => ArrayHelper::map(Teryt::findOrderStreetName(), 't_ulica', 'ulica'),
            	'filterWidgetOptions' => [
            		'pluginOptions' => ['allowClear' => true],
            	],
            	'filterInputOptions' => ['placeholder' => 'ulica'],
            	'options' => ['style'=>'width:20%;']
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
            'phone',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => InstallTask::$statusName,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                    'options' => ['multiple'=>true],
                ],
                'filterOptions' => ['prompt' => ''],
                'value' => function ($model) {
                    if ($model->status == 0) return '<span class="label label-success">'. InstallTask::$statusName[$model->status].'</span>';
                    if ($model->status == 1) return '<span class="label label-danger">'. InstallTask::$statusName[$model->status].'</span>';
                    if ($model->status == 2) return '<span class="label label-info">'. InstallTask::$statusName[$model->status].'</span>';
                },
                'options' => ['style'=>'width:12%;']
            ],
            [
                'attribute' => 'category_id',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->category_id == 1) return '<span class="label label-primary">' . InstallTask::$categoryName[$model->category_id].'</span>';
                    elseif ($model->category_id == 2) return '<span class="label label-info">' . InstallTask::$categoryName[$model->category_id].'</span>';
                    elseif ($model->category_id == 3) return '<span class="label label-warning">' . InstallTask::$categoryName[$model->category_id].'</span>';
                    elseif ($model->category_id == 100) return '<span class="label label-default">' . InstallTask::$categoryName[$model->category_id].'</span>';
                },
            	'filter' => InstallTask::$categoryName,
                'options' => ['style'=>'width:5%;'],
            ],
//             [
//                 'attribute' => 'label_id',
//                 'format' => 'raw',
//                 'value' => function ($model) {
//                     return '<span class="label label-default">' . InstallTask::$labelName[$model->label_id].'</span>';
//                 },
//                 'filter'=> InstallTask::$labelName,
//                 'options' => ['style'=>'width:5%;'],
//             ],
            [
            	'attribute' => 'create_by',
            	'value' => 'createBy.last_name',
            	'filterType' => GridView::FILTER_SELECT2,
            	'filter' => ArrayHelper::map(User::findOrderByLastName()->all(), 'id', 'last_name'),
            	'filterWidgetOptions' => [
            		'pluginOptions' => ['allowClear' => true],
                ],
            	'filterInputOptions' => ['placeholder' => ''],
            	'format' => 'raw',
            	'options' => ['style'=>'width:10%;'],
                'headerOptions' => ['class' => 'skip-export'],
                'contentOptions' => ['class' => 'skip-export']
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
                'format' => 'raw',
                'options' => ['style'=>'width:10%;'],
                'headerOptions' => ['class' => 'skip-export'],
                'contentOptions' => ['class' => 'skip-export']
            ],
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'fulfit',
                'trueLabel' => 'Tak',
                'falseLabel' => 'Nie',
                'options' => ['style'=>'width:7%;'],
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
        ],
    ]); ?>

</div>

<?php 
$js = <<<JS
$(function() {

	$('body').on('click', '.add-task', function(event){
        
		$('#modal-calendar').modal('show')
			.find('#modal-content-calendar')
			.load($(this).attr('href'));
    
        return false;
	});

	$('body').on('click', 'a[title="Zamknij"]', function(event){
        
		$('#modal-task').modal('show')
			.find('#modal-task-content')
			.load($(this).attr('href'));
    
        return false;
	});
});
JS;

$this->registerJs($js)
?>