<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Adresacja';
$this->params['breadcrumbs'][] = 'SEU';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-------------------------------------------- otwórz update okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-update-net',	
		'header' => '<center><h4>Edycja</h4></center>',
		'size' => 'modal-sm',
	]);
	
	echo "<div id='modal-content-update-net'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<div class="col-xs-3">

    <?= GridView::widget([
        'id' => 'vlan-grid',
        'dataProvider' => $dataProvider,
//         'filterModel' => $modelVlan,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'vlan-grid-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
        //'showPageSummary' => TRUE,
    	'export' => false,
        'columns' => [
            [
            	'attribute' => 'id',
            	'options' => ['style'=>'width:20%;']
            ],		
        	'desc',
        	[
        		'class' => 'yii\grid\ActionColumn',
        		'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', ['vlan/create'], ['class' => 'create-vlan']),
        		'buttons' => [
        			'view' => function ($model, $data) {
        				$url = Url::toRoute(['subnet/index', 'vlan' => $data->id]);
        				return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
        					'class' => 'show-subnet',	
        					'title' => \Yii::t('yii', 'Widok podsieci'),
        					'data-pjax' => true,
        				]);
        			},
        			'update' => function ($url, $model, $data) {
        				return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
        					'class' => 'update-vlan',
        					'title' => \Yii::t('yii', 'Edycja'),
        					'data-pjax' => '0',
        				]);
        			},
        			'delete' => function ($url, $model, $data) {
        				return Html::a('<span class="glyphicon glyphicon-trash"></span>', false, [
        					'class' => 'delete-vlan',
        					'delete-url' => $url,	
        					'title' => \Yii::t('yii', 'Usuń'),
        					//'data-pjax' => '1',
        					//'data-method' => 'post',
//         					'data' => ['confirm' => 'Czy na pewno usunąć rekord?']	
        				]);
        			},
        		]
        	],
        ]                
    ]); ?>

</div>

<div id="subnet-grid" class="subnet-grid col-xs-9"></div>
<div class="ip-grid col-xs-12"></div>

<script>
    
$(document).ready(function() {

	$('body').on('click', ".create-vlan", function(event){
        
		$('#modal-update-net').modal('show')
		.find('#modal-content-update-net')
		.load($(this).attr('href'));

    	return false;
	});
    
    $('body').on('click', ".show-subnet", function(event){
        
    	$(".subnet-grid").load($(this).attr('href'));
    
        return false;
	});

	$('body').on('click', ".update-vlan", function(event){
        
		$('#modal-update-net').modal('show')
		.find('#modal-content-update-net')
		.load($(this).attr('href'));

    	return false;
	});

	$('body').on('click', '.delete-vlan', function() {

		if (confirm('Czy na pewno usunąć rekord?')){
	    	$.ajax({
	        	url: $(this).attr('delete-url'),
	            type: "post",
	            dataType: 'json',
	            error: function(xhr, status, error) {
	            	alert('There was an error with your request.' + xhr.responseText);
	            }
	        }).done(function(data) {
	        	$.pjax.reload({container: '#vlan-grid-pjax'});
	        });
		}

		return false;             
    });
});

</script>