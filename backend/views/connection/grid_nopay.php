<?php 
use app\models\Package;
use backend\models\Address;
use backend\models\Connection;
use backend\models\Device;
use backend\models\Task;
use backend\models\Type;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Connection $model
 */

$this->params['breadcrumbs'][] = 'Niepłacący';
?>

<!-------------------------------------------- otwórz kalendarz okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-open-calendar',	
		'header' => '<center><h4>Kalendarz zadań</h4></center>',
		'size' => 'modal-lg',
	]);
	
	echo "<div id='modal-content-calendar'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->  

<?= GridView::widget([
	'id' => 'connection-grid',
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'filterSelector' => 'select[name="per-page"]',
	'pjax' => true,
	'pjaxSettings' => [
		'options' => [
			'id' => 'connection-grid-pjax'
		]
	],
	'resizableColumns' => FALSE,
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
	'summary' => 'Widoczne {count} z {totalCount}',
	'export' => false,
	'panel' => [
		'before' => $this->render('_search', [
			'searchModel' => $searchModel,
		]),
	],
	'rowOptions' => function($model){
		if((strtotime(date("Y-m-d")) - strtotime($model->start_date)) / (60*60*24) >= 21){
	
			return ['class' => 'after-date'];
		}
	},
	'columns' => [
        [
			'header'=>'Lp.',
			'class'=>'yii\grid\SerialColumn',
           	'options'=>['style'=>'width: 4%;'],
		],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'value' => function ($model, $key, $index, $column){

                return GridView::ROW_COLLAPSED;
            },
            'detail' => function($data){

                return 'Info: '.$data->info.'<br>Info Boa: '.$data->info_boa;
            },
        ],
        [
        	'attribute' => 'start_date',
        	'value'=> function ($model){
        		return date("Y-m-d", strtotime($model->start_date));
            },
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
            	'model' => $searchModel,
            	'attribute' => 'start_date',
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
            'attribute'=>'street',
            'value'=>'modelAddress.ulica',
            'filter'=> Html::activeDropDownList($searchModel, 'street', ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->orderBy('ulica')->all(), 'ulica', 'ulica'), ['prompt'=>'', 'class'=>'form-control']),
            'options' => ['style'=>'width:15%;'],
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
        [
            'attribute'=>'type',
            'value'=>'modelType.name',
            'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(Type::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
            'options' => ['style'=>'width:5%;'],
        ],
        [
	        'attribute'=>'package',
	        'value'=>'modelPackage.name',
	        'filter'=> Html::activeDropDownList($searchModel, 'package', ArrayHelper::map(Package::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
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
			'class'=>'kartik\grid\BooleanColumn',
			'attribute' => 'wire', // it can be 'attribute' => 'tableField' to.
			'header' => 'Kabel',
			'trueLabel' => 'Tak',
			'falseLabel' => 'Nie',
			'options' => ['style'=>'width:7%;'],
		],
        [
            'class'=>'kartik\grid\BooleanColumn',
            'attribute' => 'socket', // it can be 'attribute' => 'tableField' to.
            'header' => 'Gniazdo',
        	'trueLabel' => 'Tak',
        	'falseLabel' => 'Nie',
            'options' => ['style'=>'width:7%;'],
        ],
        [
        	'attribute' => 'task_id',
        	'label' => 'Montaż',
        	'format' => 'raw',	
        	'value'=> function($model, $key){
        		if (!is_null($model->task_id)){
        			if (is_object($model->task))
        				return Html::a(date('Y-m-d', strtotime($model->task->start)), Url::to(['task/install-task/view-calendar', 'connectionId' => $key]), ['class' => 'task']);
        		}
        		elseif ($model->socket > 0)
        			return null;
        		else
        			return Html::a('dodaj', Url::to(['task/install-task/view-calendar', 'connectionId' => $key]), ['class' => 'task']);
			},
			'filter' => false,
        ],
        [
        	'attribute' => 'conf_date',
        	'value'=> 'conf_date',
			'filterType' => GridView::FILTER_DATE,
			'filterWidgetOptions' => [
				'model' => $searchModel,
				'attribute' => 'conf_date',
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
            'header' => PageSize::widget([
                'defaultPageSize' => 100,
                'pageSizeParam' => 'per-page',
                'sizes' => [
                    10 => 10,
                    100 => 100,
                    500 => 500,
                    1000 => 1000,
                ],
                'template' => '{list}',
            ]),
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {tree}',
        	'buttons' => [
        		'tree' => function ($model, $data) {
        			if($data->mac && $data->port >= 0 && $data->device && !$data->nocontract && !$data->host && $data->wire > 0 && is_null($data->close_date)){
        				$url = Url::toRoute(['tree/add', 'id' => $data->id, 'host' => true]);
	        			return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
	        				'title' => \Yii::t('yii', 'Zamontuj'),
	        				'data-pjax' => '0',
	        			]);
        			} elseif ($data->host) {
        				$url = Url::toRoute(['tree/index', 'id' => $data->host . '.0']);
        				return Html::a('<span class="glyphicon glyphicon-play"></span>', $url, [
        					'title' => \Yii::t('yii', 'SEU'),
        					'data-pjax' => '0',
        				]);
        			} else
        				return null;
        		},
        	]
        ],            
    ]
]); 
?>

<script>

$(function(){
	$('body').on('click', '.task', function(event){
        
		$('#modal-open-calendar').modal('show')
			.find('#modal-content-calendar')
			.load($(this).attr('href'));
    
        return false;
	});
});

</script>