<?php

use backend\models\Address;
use backend\models\AddressShort;
use backend\modules\task\models\InstallTaskSearch;
use backend\modules\task\models\TaskCategory;
use backend\modules\task\models\TaskType;
use common\models\User;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\modules\task\models\InstallTaskSearch $searchModel
 */

$this->params['breadcrumbs'][] = 'Do zrobienia';

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
            'fontAwesome' => true,
            'showConfirmAlert' => false,
            'target'=>GridView::TARGET_BLANK,
            'exportConfig' => ['pdf' => TRUE],
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
                    return $model->description;
                },
            ],
            [
                'attribute' => 'start',
                'label' => 'Dzień',
                'value'=> function ($model){
                	return date("Y-m-d", strtotime($model->start));
                },
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                	'model' => $searchModel,
                	'attribute' => 'start',
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
            	'attribute' => 'start',
            	'label' => 'Od',
            	'filter' => false,	
            	'value' => function ($model){
            		return date("H:i", strtotime($model->start));
            	},	
                'options' => ['id'=>'start', 'style'=>'width:5%;']
			],
			[
				'attribute' => 'end',
				'label' => 'Do',
				'filter' => false,
				'value' => function ($model){
					return date("H:i", strtotime($model->end));
            	},
            	'options' => ['id'=>'start', 'style'=>'width:5%;']
            ],
            [
            	'attribute' => 'street',
            	'value' => 'address.ulica',
            	'filterType' => GridView::FILTER_SELECT2,
            	'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
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
                'attribute' => 'type_id',
                'value' => 'type.name',
            	'filter' => ArrayHelper::map(TaskType::findWhereType(1)->all(), 'id', 'name'),
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute' => 'category_id',
                'value' => 'category.name',
                'filter'=> ArrayHelper::map(TaskCategory::findWhereType(1)->all(), 'id', 'name'),
                'options' => ['style'=>'width:5%;'],
            ],
            [
	            'class'=>'kartik\grid\BooleanColumn',
	            'header'=>'Umowa',
	            'attribute'=>'nocontract',
	            'trueLabel' => 'Nie',
	            'falseLabel' => 'Tak',
	            'trueIcon' => GridView::ICON_INACTIVE,
	            'falseIcon' => GridView::ICON_ACTIVE,
	            'options' => ['style'=>'width:5%;'],
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
//             [
//                 'attribute' => 'add_user',
//                 'value' => 'addUser.last_name'
//             ],
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