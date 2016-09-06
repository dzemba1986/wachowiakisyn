<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\DeviceType;
use backend\models\Manufacturer;
use backend\models\Model;
use yii\bootstrap\Modal;
use nterms\pagesize\PageSize;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Magazyn';
$this->params['breadcrumbs'][] = 'SEU';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-------------------------------------------- dodaj do magazynu okno modal-------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-add-store',	
		'header' => '<center><h4>Dodaj do magazynu</h4></center>',
		'size' => 'modal-sm',	
	]);
	
	echo "<div id='modal-content-add-store'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<!-------------------------------------------- dodaj na drzewo okno modal --------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal_add_tree',	
		'header' => '<center><h4>Dodaj do drzewa</h4></center>',
		'size' => 'modal-mg',
		'options' => [
			'tabindex' => false // important for Select2 to work properly
		],
	]);
	
	echo "<div id='modal_content_add_tree'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<!------------------------------------------ update urządzenia -  okno modal ------------------------------------------>

	<?php Modal::begin([
		'id' => 'modal-update-store',	
		'header' => '<center><h4>Edytuj urządzenie</h4></center>',
		'size' => 'modal-sm',	
	]);
	
	echo "<div id='modal-content-update-store'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<!------------------------------------------ delete urządzenia -  okno modal ------------------------------------------>

	<?php Modal::begin([
		'id' => 'modal_delete_store',	
		'header' => '<center><h4>Czy na pewno usunąć?</h4></center>',
		'size' => 'modal-sm',
		'closeButton' => ['id' => 'close-button'],
	]);
	
	echo "<div id='modal_content_delete_store'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->   

<!------------------------------------------ formularz dodawania do magazynu ------------------------------------------>

<?php $form = ActiveForm::begin([
	'action' => ['store/add'],
	'id' => 'add-store-form'
]); ?>

<div class="row">
<?= $form->field($searchModel, 'type', [
		'options' => ['class' => 'col-md-2', 'style' => 'padding-right: 5px;'],
		'template' => "{input}\n{hint}\n{error}"
	])->dropDownList(
		ArrayHelper::map(DeviceType::find()->where(['<>', 'name', 'Host'])->andWhere(['<>', 'name', 'ROOT'])->orderBy('name')->all(), 'id', 'name'),
		[
				'prompt' => 'Wybierz typ',
		]
		)?>

	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-success add-store col-xs-1']) ?>

</div>
<?php ActiveForm::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->   

<div class="store">
    
<!--gridview magazyn-->
    <?= GridView::widget([
        'id' => 'store-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'store-grid-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
        //'showPageSummary' => TRUE,
    	'export' => false,
       'panel' => [
               'heading'=>'Magazyn',
       ],
        'columns' => [
            [
                'class'=>'yii\grid\SerialColumn',
                'options'=>['style'=>'width: 4%;'],
            ],        
            [
                'attribute'=>'type',
                'value'=>'modelType.name',
                'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(DeviceType::find()->orderBy('name')->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
                //'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'manufacturer',
                'value'=>'modelManufacturer.name',
                'filter'=> Html::activeDropDownList($searchModel, 'manufacturer', ArrayHelper::map(Manufacturer::find()->orderBy('name')->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
                //'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'model',
                'value'=>'modelModel.name',
                'filter'=> Html::activeDropDownList($searchModel, 'model', ArrayHelper::map(Model::find()->orderBy('name')->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
                //'options' => ['style'=>'width:5%;'],
            ],
//         	[
//         		'attribute'=>'model',
//         		'value'=>'modelModel.name',
//         		'filter'=> Html::activeDropDownList($searchModel, 'model', ArrayHelper::map(Model::find()->orderBy('name')->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
//         		//'options' => ['style'=>'width:5%;'],
//         	],
            'serial',
            'mac',
            //'name',
            //'desc',
            [   
	            'header' => PageSize::widget([
	                'defaultPageSize' => 100,
	                'pageSizeParam' => 'per-page',
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
	            'template' => '{update} {tree} {delete}',    
	            'buttons' => [
	                'tree' => function ($model, $data) {
                        $url = Url::toRoute(['tree/add', 'id' => $data->id]);
                        return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
                                    'title' => \Yii::t('yii', 'Zamontuj'),
                                    'data-pjax' => '0',
                        ]);
                    },
// 	            	'update' => function ($model, $data) {
// 	            		$url = Url::toRoute(['device/update-on-store', 'id' => $data->id]);
// 	            	}
	            ]
            ],  
        ]                
    ]); ?>
 <!--koniec gridview magazyn-->  

</div>

<script>
    
$(document).ready(function() {
    
    $('body').on('click', "a[title='Update']", function(event){
        
        event.preventDefault();
        
		$('#modal-update-store').modal('show')
			.find('#modal-content-update-store')
			.load($(this).attr('href'));
    
        return false;
	});

	$('body').on('click', "a[title='Zamontuj']", function(event){
        
        event.preventDefault();
        
		$('#modal_add_tree').modal('show')
			.find('#modal_content_add_tree')
			.load($(this).attr('href'));
    
        return false;
	});
    
    $('body').on('click', '.add-store', function(event){

		if( !$("select[name='DeviceSearch[type]']").val() )
			alert("Nie wybrano typu urządzenia!");
		else {
		$('#modal-add-store').modal('show')
			.find('#modal-content-add-store')
			.load($("#add-store-form").attr('action') + '&type=' + $("select[name='DeviceSearch[type]']").val());
    	}
	
        return false;
	});
    
    $('body').on('click', "a[title='Delete']", function(event){
        
        event.preventDefault();
        
        $('#modal_delete_store').modal('show')
		.find('#modal_content_delete_store')
		.load($(this).attr('href'));

    	return false;       		
	});
        
	
});

</script>