<?php

use backend\models\AddressShort;
use backend\models\InstallationType;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\InstallationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Instalacje';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-------------------------------------------- edycja instalacji okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-installation-update',	
		'header' => '<center><h4>Edycja instalacji</h4></center>',
		'size' => 'modal-sm',
		'options' => [
				'tabindex' => false
		],
	]);
	
	echo "<div id='modal-content-installation-update'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<div class="installation-index">

    <?= GridView::widget([
        'id' => 'installation-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'installation-grid-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
        'formatter' => [
			'class' => 'yii\i18n\Formatter',
			'nullDisplay' => ''
		],
    	'summary' => 'Widoczne {count} z {totalCount}',
    	'export' => FALSE,
        'panel' => [
                'heading'=> '',
        ],
        'columns' => [
            [
                'header'=>'Lp.',
                'class'=>'yii\grid\SerialColumn',
                'options'=>['style'=>'width: 4%;'],
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
                'options'  => ['style'=>'width:5%;'],
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
                'filter' => ArrayHelper::map(InstallationType::find()->all(), 'id', 'name'),
                'options' => ['style'=>'width:10%;'],
            ],
        	[
        		'attribute' => 'wire_date',
        		'value'=> 'wire_date',
        		'filterType' => GridView::FILTER_DATE,
        		'filterWidgetOptions' => [
        			'model' => $searchModel,
        			'attribute' => 'wire_date',
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
        		'attribute' => 'socket_date',
        		'value'=> 'socket_date',
        		'filterType' => GridView::FILTER_DATE,
        		'filterWidgetOptions' => [
        			'model' => $searchModel,
        			'attribute' => 'socket_date',
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
                'attribute' => 'wire_length',
                'options' => ['style'=>'width:5%;'],
            ],    
            'wire_user',
            'socket_user',
        	[
        		'class'=>'kartik\grid\BooleanColumn',
        		'attribute' => 'status',
        		'trueLabel' => 'Istnieje',
        		'falseLabel' => 'Nieistnieje',
        		'options' => ['style'=>'width:5%;'],
        	],
            [   
            'header' => PageSize::widget([
                'defaultPageSize' => 100,
                'sizes' => [
                    10 => 10,
                    100 => 100,
                    500 => 500,
                    1000 => 1000,
                ],
                'template' => '{list}',
            ]),
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
        ],     
        ],
    ]); ?>

</div>

<script>
    
$(document).ready(function() {

	$(function(){
		$('body').on('click', 'a[title="Update"]', function(event){
	        
			$('#modal-installation-update').modal('show')
				.find('#modal-content-installation-update')
				.load($(this).attr('href'));
	    
	        return false;
		});
	});
});

</script>
