<?php 
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

$this->params['breadcrumbs'][] = 'Bez kabla';
?>

<!-------------------------------------------- dodanie istalacji okno modal ------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-create-installation',	
		'header' => '<center><h4>Dodaj instalację</h4></center>',
		'size' => 'modal-mm',
		'options' => [
			'tabindex' => false // important for Select2 to work properly
		],			
	]);
	
	echo "<div id='modal-create-installation-content'></div>";
	
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
	'summary' => 'Widoczne {count} z {totalCount}',
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
	'export' => false,
	'panel' => [
			'before' => $this->renderAjax('_search', [
					'searchModel' => $searchModel,
			]),
	],
	'rowOptions' => function($model){
		if((strtotime(date("Y-m-d")) - strtotime($model->start_date)) / (60*60*24) >= 21){
	
			return ['class' => 'afterdate'];
		}
		elseif ($model->pay_date <> null) {
	
			return ['class' => 'activ'];
		}
		elseif ($model->close_date <> null) {
	
			return ['class' => 'inactiv'];
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
            'value' => function ($model, $key, $index, $column){

                return GridView::ROW_COLLAPSED;
            },
            'detail' => function($data){

                return 'Info: '.$data->info.'<br>Info Boa: '.$data->info_boa;
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
        			'endDate' => '0d',
        		]
        	],
        	'options' => ['id'=>'start', 'style'=>'width:13%;'],
        ],
        [	
            'attribute' => 'street',
            'value' => 'address.ulica',
            'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
            'options' => ['style'=>'width:15%;'],
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
           	'attribute' => 'flat_detail',
           	'value' => 'address.lokal_szczegol',
           	'options' => ['style'=>'width:10%;'],
       	],
        [
            'attribute' => 'type_id',
            'value' => 'type.name',
            'filter' => ArrayHelper::map(ConnectionType::find()->all(), 'id', 'name'),
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'header' => 'Kabel',
            'format' => 'raw',
            'value' => function ($model, $key){
                return Html::a('dodaj', Url::to(['installation/create', 'connectionId' => $key]), ['class' => 'create-installation']);
            },            
            'options' => ['style'=>'width:7%;'],
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
        	'attribute' => 'task_id',
        	'label' => 'Montaż',
        	'format' => 'raw',
        	'value' => function ($model){
        		if ($model->task)
        			return date("Y-m-d", strtotime($model->task->start));
            },
        	'filterType' => GridView::FILTER_DATE,
        	'filterWidgetOptions' => [
        		'model' => $searchModel,
        		'attribute' => 'task_id',
        		'pickerButton' => false,
        		'language' => 'pl',
        		'pluginOptions' => [
        			'format' => 'yyyy-mm-dd',
        			'todayHighlight' => true,
        		]
        	],
        	'options' => ['id'=>'start', 'style'=>'width:13%;'],
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
            'template' => '{view} {update}',
        ],              
    ]
]); 

$this->registerJs(
'$(function(){
	$("body").on("click", ".create-installation", function(event){
       
		$("#modal-create-installation").modal("show")
		.find("#modal-create-installation-content")
		.load($(this).attr("href"));
   
    	return false;
	});
});'
);
?>