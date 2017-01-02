<?php 
use kartik\grid\GridView;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\Address;
use backend\models\Type;
use backend\models\Task;
use nterms\pagesize\PageSize;
use yii\bootstrap\Modal;

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
	//'showPageSummary' => TRUE,
	'export' => false,
	'panel' => [
			'before' => $this->render('_search', [
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
            'attribute'=>'start_date',
            'value'=>'start_date',
            'format'=>'raw',
            'filter'=>	DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'start_date',
                'removeButton' => FALSE,
                'language'=>'pl',	
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d', //wybór daty max do dziś
                ]
            ]),
            'options' => ['id'=>'start', 'style'=>'width:10%;'],
            
        ],		
        [	
            'attribute'=>'street',
            'value'=>'modelAddress.ulica',
            'filter'=> Html::activeDropDownList($searchModel, 'street', ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica'), ['prompt'=>'', 'class'=>'form-control']),
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
           	'attribute'=>'flat_detail',
           	'value'=>'modelAddress.lokal_szczegol',
           	'options' => ['style'=>'width:10%;'],
       	],
        [
            'attribute'=>'type',
            'value'=>'modelType.name',
            'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(Type::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'header' => 'Kabel',
            'format' => 'raw',
            'value' => function ($data){
                return Html::a('dodaj', Url::to(['installation/create', 'conId' => $data->id]), ['class' => 'create-installation']);
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
            'attribute'=>'task',
            'label' => 'Montaż',
            'format'=>'raw',
//             'value'=> function($data){
//                 if($data->task != NULL){
                    
//                     //$date = new DateTime($data->modelTask->start_date);
//                     //return $date->format('Y-m-d');
//                     return 'modelTask.start_date';
//                 }
//                 else
//                     return 'brak';
//             },
            'value' => 'modelTask.start_date',
            'filter' =>	DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'task',
                'removeButton' => FALSE,
                'language'=>'pl',	
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    //'endDate' => '0d', //wybór daty max do dziś
                ]
            ]),
            'options' => ['style'=>'width:10%;'],
        ],            
        [   
            'header' => PageSize::widget([
                'defaultPageSize' => 100,
                'sizes' => [
                    10 => 10,
                    100 => 100,
                    500 => 500,
                    1000 => 1000,
                    //5000 => 5000,
                ],
                'template' => '{list}',
            ]),
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update}',
        ],              
    ]
]); 
?>

<script>
$(function(){

	$("body").on('click', '.create-installation', function(event){
        
		$('#modal-create-installation').modal('show')
			.find('#modal-create-installation-content')
			.load($(this).attr('href'));
    
        return false;
	});
	
});
</script>