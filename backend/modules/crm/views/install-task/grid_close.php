<?php

use backend\modules\address\models\AddressShort;
use common\models\User;
use common\modules\crm\models\TaskCategory;
use common\modules\crm\models\TaskType;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;

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
        'resizableColumns' => FALSE,
    	'summary' => 'Widoczne {count} z {totalCount}',
    	'formatter' => [
    		'class' => 'yii\i18n\Formatter',
    		'nullDisplay' => ''
    	],
        'export'=>[
            'fontAwesome'=>true,
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK,
            'exportConfig' => ['pdf' => TRUE, 'json' => FALSE],
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
                'value' => function ($model, $key, $index, $column){

                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function($data){

                    return $data->description;
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
            	//'format' => 'raw',
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
            'cost',
            [
            	'class' => 'kartik\grid\BooleanColumn',
            	'attribute' => 'status',
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
           		'label' => 'Zamknięto'
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
                'template' => '{delete}',
            ],     
        ],
    ]); ?>

</div>

<script>
    
$(document).ready(function() {

    //reinicjalizacja kalendarza z datami po użyciu pjax'a
    $("#task-grid-pjax").on("pjax:complete", function() {
        
        if (jQuery('#tasksearch-start').data('kvDatepicker')) { 
            jQuery('#tasksearch-start').kvDatepicker('destroy'); 
        }
        jQuery('#tasksearch-start-kvdate').kvDatepicker(kvDatepicker_00747738);

        initDPAddon('tasksearch-start');
    });
});

</script>