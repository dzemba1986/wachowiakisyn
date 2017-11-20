<?php

use backend\models\DeviceType;
use backend\modules\task\models\DeviceTaskSearch;
use common\models\User;
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

$this->params['breadcrumbs'][] = 'Do zrobienia';

require_once '_modal_task.php';
require_once '_modal_comment.php';
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
                    return $model->description;
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
            	'template' => '{close} {addcomment} {comments}',
            	'buttons' => [
            		'close' => function ($model, $data) {
            			$url = Url::to(['device-task/close', 'id' => $data->id]);
            			
            			return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
            				'title' => \Yii::t('yii', 'Zamknij'),
            				'data-pjax' => '0',
            			]);
            		},
            		'addcomment' => function ($model, $data) {
            			$url = Url::to(['comment/create', 'taskId' => $data->id]);
            		
            			return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
            				'title' => \Yii::t('yii', 'Dodaj komentarz'),
            				'data-pjax' => '0',
            			]);
            		},
            		'comments' => function ($url, $model) {
            		
            			if ($model->getComments()->count() == 0){
            				return null;
            			} else {
	            			$url = Url::to(['comment/index', 'taskId' => $model->id]);
	            		
	            			return Html::a('<span class="glyphicon glyphicon-comment"></span>', $url, [
	            				'title' => \Yii::t('yii', 'Komentarze'),
	            				'data-pjax' => '0',
	            			]);
            			}
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

	$('body').on('click', 'a[title="Dodaj komentarz"]', function(event){
        
		$('#modal-comment').modal('show')
			.find('#modal-comment-content')
			.load($(this).attr('href'));
    
        return false;
	});

	$('body').on('click', 'a[title="Komentarze"]', function(event){
        
		$('#modal-comment').modal('show')
			.find('#modal-comment-content')
			.load($(this).attr('href'));
    
        return false;
	});
});
JS;

$this->registerJs($js)
?>