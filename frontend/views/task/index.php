<?php

use backend\modules\task\models\DeviceTaskSearch;
use common\models\User;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var DeviceTaskSearch $searchModel
 */

$this->params['breadcrumbs'][] = 'Zgłoszenia';

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
        'columns' => [
            [
                'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['task/create']), ['class' => 'add-task']),
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
                'attribute'=>'create',
                'value'=> function ($model){
                	return $model->create ? date("Y-m-d", strtotime($model->create)) : null;
                },
                'format'=>'raw',
                'filter'=>	DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'create',
                    'removeButton' => FALSE,
                    'language'=>'pl',	
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'endDate' => '0d', //wybór daty max do dziś
                    ]
                ]),
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
            				'url' => Yii::$app->urlManagerBackend->baseUrl . '/index.php?r=camera%2Fsearch',	//http://localhost/backend/index.php?r=device/list
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
            	'attribute' => 'close_user',
            	'value' => 'closeUser.last_name',
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
            	'attribute' => 'close',
            	'value'=> function ($model){
            		return $model->close ? date("Y-m-d", strtotime($model->close)) : null;
            	},
            	'format'=>'raw',
            	'filter'=>	DatePicker::widget([
            		'model' => $searchModel,
            		'attribute' => 'close',
            		'removeButton' => FALSE,
            		'language'=>'pl',
            		'pluginOptions' => [
            			'format' => 'yyyy-mm-dd',
            			'todayHighlight' => true,
            			'endDate' => '0d', //wybór daty max do dziś
            		]
            	]),
            ],
            [
            	'attribute' => 'status',
            	'format'=>'raw',
            	'filter' => ['null' => 'W trakcie', true => 'Zrobione', false => 'Niezrobione'],	
            	'filterOptions' => ['prompt' => ''],
            	//'filterInputOptions' => ['prompt' => ''],
            	'value' => function ($model){
            		if ($model->status) return '<span class="glyphicon glyphicon-ok text-success"></span>';
            		elseif (is_null($model->status)) return '<span class="glyphicon glyphicon-refresh"></span>';
            		else return '<span class="glyphicon glyphicon-remove text-danger"></span>';
            	}
            ],
            [   
                'header' => PageSize::widget([
                    'defaultPageSize' => 100,
                    'sizes' => [
                        10 => 10,
                        100 => 100,
                        500 => 500,
                        1000 => 1000,
                    ],
                    'template' => '{list}',
                ]),
            ],     
        ],
    ]); ?>

</div>

<?php 
$js = <<<JS
$(function() {

	$('body').on('click', '.add-task', function(event){
        
		$('#modal-task').modal('show')
			.find('#modal-task-content')
			.load($(this).attr('href'));
    
        return false;
	});


    //reinicjalizacja filtrów po użyciu pjax'a
    $("#task-grid-pjax").on("pjax:complete", function() {
        
        if (jQuery('#devicetasksearch-create').data('kvDatepicker')) { 
            jQuery('#devicetasksearch-create').kvDatepicker('destroy'); 
        }
        jQuery('#devicetasksearch-create-kvdate').kvDatepicker(kvDatepicker_d5532c14);
        initDPAddon('devicetasksearch-create');

		if (jQuery('#devicetasksearch-close').data('kvDatepicker')) { 
			jQuery('#devicetasksearch-close').kvDatepicker('destroy'); 
		}
		jQuery('#devicetasksearch-close-kvdate').kvDatepicker(kvDatepicker_d5532c14);
		initDPAddon('devicetasksearch-close');
    });
});
JS;

$this->registerJs($js)
?>