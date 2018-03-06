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
            	'attribute' => 'create',
            	'label' => 'Dzień',	
            	'value'=> function ($model){
            		return date("Y-m-d", strtotime($model->create));
            	},
            	'filterType' => GridView::FILTER_DATE,
            	'filterWidgetOptions' => [
            		'model' => $searchModel,
            		'attribute' => 'create',
            		'pickerButton' => false,
            		'language' => 'pl',
            		'pluginOptions' => [
            			'format' => 'yyyy-mm-dd',
            			'todayHighlight' => true,
            			'endDate' => '0d'
            		]
            	],
            'options' => ['id'=>'start', 'style'=>'width:10%;'],
            ],
            [
            	'attribute' => 'create',
            	'label' => 'Godzina',	
            	'value'=> function ($model){
            		return date("H:i", strtotime($model->create));
            	},
            	'filter' => false,
            	'options' => ['id'=>'start', 'style'=>'width:10%;'],
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
            				'url' => Url::to(['device/list-camera']),
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
            		if ($model->status) return '<span class="glyphicon glyphicon-ok text-success"></span>';
            		elseif (is_null($model->status)) return '<span class="glyphicon glyphicon-refresh"></span>';
            		else return 'do wymiany';
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
});
JS;

$this->registerJs($js)
?>