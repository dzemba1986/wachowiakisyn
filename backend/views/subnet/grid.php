<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<!-------------------------------------------- otwórz update okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-update-dhcp-value',	
		'header' => '<center><h4>Edycja</h4></center>',
		'size' => 'modal-lg',
	]);
	
	echo "<div id='modal-content-update-dhcp-value'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

    <?= GridView::widget([
        'id' => 'subnet-grid',
        'dataProvider' => $dataProvider,
        //'filterModel' => $modelSubnet,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'subnet-grid-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
        //'showPageSummary' => TRUE,
    	'export' => false,
        'columns' => [
            'ip',		
        	'desc',
        	[
        		'class' => 'kartik\grid\BooleanColumn',
        		'attribute' => 'dhcp',
        		'trueLabel' => 'Tak',
        		'falseLabel' => 'Nie',
        	],
        	//'size',
        	[
        		'header' => 'Wolne',
        		'value' => 'ipFreeCount'
        	],
        	[
        		'class' => 'yii\grid\ActionColumn',
        		'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', ['subnet/create', 'vlan' => $vlan], ['class' => 'create-subnet']),
        		'template' => '{view} {update} {delete} {dhcp}',
        		'buttons' => [
        			'view' => function ($model, $data) {
        				$url = Url::toRoute(['ip/grid', 'subnet' => $data->id]);
        				return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
        					'class' => 'show-ip',	
        					'title' => \Yii::t('yii', 'Widok'),
        					'data-pjax' => true,
        				]);
        			},
        			'update' => function ($url, $model, $data) {
        				return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
        					'class' => 'update-subnet',
        					'title' => \Yii::t('yii', 'Edycja'),
        					'data-pjax' => '0',
        				]);
        			},
        			'delete' => function ($url, $model, $data) {
        				return Html::a('<span class="glyphicon glyphicon-trash"></span>', false, [
        					'class' => 'delete-subnet',
        					'delete-url' => $url,	
        					'title' => \Yii::t('yii', 'Usuń'),
        					//'data-pjax' => '1',
        					//'data-method' => 'post',
//         					'data' => ['confirm' => 'Czy na pewno usunąć rekord?']	
        				]);
        			},
        			'dhcp' => function ($model, $data) {
	        			if($data->dhcp){
	        				$url = Url::toRoute(['dhcp-value/update', 'subnet' => $data->id]);
	        				return Html::a('D', $url, [
        						'class' => 'dhcp',
        						'title' => \Yii::t('yii', 'DHCP'),
        						'data-pjax' => '0',
	        				]);
	        			} else
	        				return null;
        			},
        		]
        	],
        ]                
    ]); ?>

<script>
    
$(document).ready(function() {

	$('body').on('click', ".create-subnet", function(event){
        
		$('#modal-update-net').modal('show')
		.find('#modal-content-update-net')
		.load($(this).attr('href'));

    	return false;
	});
    
    $('body').on('click', ".show-ip", function(event){
        
    	$(".ip-grid").load($(this).attr('href'));
    
        return false;
	});

	$('body').on('click', ".update-subnet", function(event){
        
		$('#modal-update-net').modal('show')
		.find('#modal-content-update-net')
		.load($(this).attr('href'));

    	return false;
	});

	$('body').on('click', '.delete-subnet', function() {

		if (confirm('Czy na pewno usunąć rekord?')){
	    	$.ajax({
	        	url: $(this).attr('delete-url'),
	            type: "post",
	            dataType: 'json',
	            error: function(xhr, status, error) {
	            	alert('There was an error with your request.' + xhr.responseText);
	            }
	        }).done(function(data) {
	        	$.pjax.reload({container: '#subnet-grid-pjax'});
	        });
		}

		return false;             
    });

	$('body').on('click', ".dhcp", function(event){
        
		$('#modal-update-dhcp-value').modal('show')
		.find('#modal-content-update-dhcp-value')
		.load($(this).attr('href'));

    	return false;
	});
});

</script>