<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use backend\models\Address;
use backend\models\TaskType;
use backend\models\TaskCategory;
use nterms\pagesize\PageSize;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\ModyficationSearch $searchModel
 */

$this->params['breadcrumbs'][] = 'Do zrobienia';
?>

<!-------------------------------------------- widok zamknij zadanie okno modal --------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-close-task',	
		'header' => '<center><h4>Zamknij zadanie</h4></center>',
		'size' => 'modal-sm',	
	]);
	
	echo "<div id='modal-content-close-task'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<!-------------------------------------------- otwórz kalendarz okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-open-calendar',	
		'header' => '<center><h4>Kalendarz zadań</h4></center>',
		'size' => 'modal-lg',
	]);
	
	echo "<div id='modal-content-calendar'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

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
        'panel' => [
                //'heading'=> true,
               'before' => '',
        ],
//        'rowOptions' => function($model){
//            if((strtotime(date("Y-m-d")) - strtotime($model->start_date)) / (60*60*24) >= 21){
//                
//                return ['class' => 'afterdate'];
//            }
//            elseif ($model->activ_date <> null) {
//                
//                return ['class' => 'activ'];
//            }
//            elseif ($model->resignation_date <> null) {
//            
//                return ['class' => 'inactiv'];
//            }
//        },
        'columns' => [
            [
                'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['task/view-calendar']), ['class' => 'task']),
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
                'attribute'=>'start_date',
                'label' => 'Dzień',
                'value'=> function ($data){
                    
                    $time = new DateTime($data->start);
                    return $time->format('Y-m-d');                   
                },
                'format'=>'raw',
                'filter'=>	DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'start_date',
                    'removeButton' => FALSE,
                    'language'=>'pl',	
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        //'endDate' => '0d', //wybór daty max do dziś
                    ]
                ]),
                'options' => ['id'=>'start', 'style'=>'width:8%;'],
            ],
            [
                'attribute'=>'start_time',
                'label' => 'Od',
                'value'=> function ($data){
                    
                    $time = new DateTime($data->start_time);
                    return $time->format('H:i');                   
                },
//                'format'=>'raw',
//                'filter' => FALSE,        
//                'filter'=>	DatePicker::widget([
//                    'model' => $searchModel,
//                    'attribute' => 'start',
//                    'removeButton' => FALSE,
//                    'language'=>'pl',	
//                    'pluginOptions' => [
//                        'format' => 'H:i:s',
//                        'todayHighlight' => true,
//                        //'endDate' => '0d', //wybór daty max do dziś
//                    ]
//                ]),
                'options' => ['id'=>'start', 'style'=>'width:8%;'],
            ],
            [
                'attribute'=>'end_time',
                'label' => 'Do',
                'value'=> function ($data){
                    
                    $time = new DateTime($data->end_time);
                    return $time->format('H:i');                   
                },
//                'format'=>'raw',
//                'filter' => FALSE,        
//                'filter'=>	DatePicker::widget([
//                    'model' => $searchModel,
//                    'attribute' => 'start',
//                    'removeButton' => FALSE,
//                    'language'=>'pl',	
//                    'pluginOptions' => [
//                        'format' => 'H:i:s',
//                        'todayHighlight' => true,
//                        //'endDate' => '0d', //wybór daty max do dziś
//                    ]
//                ]),
                'options' => ['id'=>'start', 'style'=>'width:8%;'],
            ],            
            [	
                'attribute'=>'street',
                'value'=>'modelAddress.ulica',
                'filter'=> Html::activeDropDownList($searchModel, 'street', ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica'), ['prompt'=>'', 'class'=>'form-control']),
                'options' => ['style'=>'width:12%;'],
            ],	
            [
                'attribute'=>'house',
                'value'=>'modelAddress.dom',
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'house_detail',
                'value'=>'modelAddress.dom_szczegol',
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'flat',
                'value'=>'modelAddress.lokal',
                'options' => ['style'=>'width:5%;'],
            ],
            'phone',            
            [
                'attribute'=>'type',
                'value'=>'modelTaskType.name',
                'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(TaskType::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'category',
                'value'=>'modelTaskCategory.name',
                'filter'=> Html::activeDropDownList($searchModel, 'category', ArrayHelper::map(TaskCategory::find()->orderBy('name')->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute' => 'add_user',
                'value' => 'modelAddUser.last_name'
            ],
//            [
//                'attribute' => 'close_user',
//                'value' => 'modelCloseUser.last_name'
//            ],
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
                'template' => '{close}{delete}',
            	'buttons' => [
            		'close' => function ($model, $data) {
            			$url = Url::toRoute(['task/close', 'id' => $data->id]);
            			
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

<script>
    
$(function() {

	$('body').on('click', 'a[title="Zamknij"]', function(event){
        
		$('#modal-close-task').modal('show')
			.find('#modal-content-close-task')
			.load($(this).attr('href'));
    
        return false;
	});

	$('body').on('click', '.task', function(event){
        
        event.preventDefault();
        
		$('#modal-open-calendar').modal('show')
			.find('#modal-content-calendar')
			.load($(this).attr('href'));
    
        return false;
	});
	
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