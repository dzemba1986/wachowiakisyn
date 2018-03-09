<?php 
use app\models\Package;
use backend\models\AddressShort;
use backend\models\ConnectionType;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var backend\models\ConnectionSearch $modelSearch
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
		'before' => $this->renderAjax('_search', [
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
			'header' => 'Lp.',
			'class' => 'kartik\grid\SerialColumn',
           	'options' => ['style'=>'width: 4%;'],
            'mergeHeader' => true
		],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'value' => function ($model){

                return GridView::ROW_COLLAPSED;
            },
            'detail' => function($model){

                return 'Info: '.$model->info.'<br>Info Boa: '.$model->info_boa;
            },
        ],
        [
        	'attribute' => 'start_date',
            'value'=> function($model) { return date('Y-m-d', strtotime($model->start_date)); },
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
            'attribute' => 'street',
            'value' => 'address.ulica',
            'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
            'options' => ['style'=>'width:12%;'],
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
            'filter' => ArrayHelper::map(ConnectionType::find()->all(), 'id', 'name'),
            'options' => ['style'=>'width:5%;'],
        ],
        [
	        'attribute' => 'package_id',
	        'value' => 'package.name',
            'filter' => ArrayHelper::map(Package::find()->all(), 'id', 'name'),
	        'options' => ['style'=>'width:5%;'],
        ],
        [
            'class' => 'kartik\grid\BooleanColumn',
        	'header' => 'Umowa',
            'attribute' => 'nocontract',
            'trueLabel' => 'Nie', 
            'falseLabel' => 'Tak',
        	'trueIcon' => GridView::ICON_INACTIVE,
        	'falseIcon' => GridView::ICON_ACTIVE,
            'options' => ['style'=>'width:5%;'],
        ],
		[
			'class' => 'kartik\grid\BooleanColumn',
			'attribute' => 'wire',
			'header' => 'Kabel',
			'trueLabel' => 'Tak',
			'falseLabel' => 'Nie',
			'options' => ['style'=>'width:7%;'],
		],
        [
            'class' => 'kartik\grid\BooleanColumn',
            'attribute' => 'socket',
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
			'options' => ['style'=>'width:10%;'],
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
                'label' => 'Ilość',
                'template' => '{label}{list}',
                'options' => ['class'=>'form-control'],
            ]),
            'class' => 'kartik\grid\ActionColumn',
            'mergeHeader' => true,
            'template' => '{view} {update} {tree}',
            'options' => ['style' => 'width:6%;'],
        	'buttons' => [
        		'tree' => function ($url, $model, $key) {
        			if($model->canConfigure()){
        				$url = Url::toRoute(['tree/add-host', 'connectionId' => $key]);
	        			return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
	        				'title' => \Yii::t('yii', 'Zamontuj'),
	        				'data-pjax' => '0',
	        			]);
        			} elseif ($model->host_id) {
        				$url = Url::toRoute(['tree/index', 'id' => $model->host_id . '.0']);
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
 
$this->registerJs(
'$(function(){
	$("body").on("click", ".task", function(event){
		$("#modal-open-calendar").modal("show")
			.find("#modal-content-calendar")
			.load($(this).attr("href"));
   
       	return false;
	});
});'
);
?>