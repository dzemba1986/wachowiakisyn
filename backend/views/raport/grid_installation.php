<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use backend\models\Address;
use backend\models\Type;
use nterms\pagesize\PageSize;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\InstallationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Instalacje';
$this->params['breadcrumbs'][] = $this->title;
?>
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
    	'summary' => 'Widoczne {count} wierszy z {totalCount}',
    	'showPageSummary'=>true,
    	'export' => FALSE,
        'panel' => [
        	'heading'=> '',
			'before' => $this->render('_search_installation', [
					'searchModel' => $searchModel,
			]),
	],
        'columns' => [
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
                'class'=>'kartik\grid\SerialColumn',
            	'pageSummary' => 'Łącznie',
                'options'=>['style'=>'width: 4%;'],
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
            [
                'attribute'=>'type',
                'value'=>'modelType.name',
                'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(Type::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'wire_date',
                'value'=>'wire_date',
                'format'=>'raw',
                'filter'=>	DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'wire_date',
                    'removeButton' => FALSE,
                    'language'=>'pl',	
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'endDate' => '0d', //wybór daty max do dziś
                    ]
                ]),
                'options' => ['id'=>'start', 'style'=>'width:8%;'],
            
            ],
            [
                'attribute'=>'socket_date',
                'value'=>'socket_date',
                'format'=>'raw',
                'filter'=>	DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'socket_date',
                    'removeButton' => FALSE,
                    'language'=>'pl',	
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'endDate' => '0d', //wybór daty max do dziś
                    ]
                ]),
                'options' => ['id'=>'start', 'style'=>'width:8%;'],
            ],
            [
                'attribute' => 'wire_length',
            	'pageSummary'=>true,
                'options' => ['style'=>'width:5%;'],
            ],    
//             'wire_user',
//             'socket_user',
//             [   
// 	            'header' => PageSize::widget([
// 	                'defaultPageSize' => 100,
// 	                'sizes' => [
// 	                    10 => 10,
// 	                    100 => 100,
// 	                    500 => 500,
// 	                    1000 => 1000,
// 	                    //5000 => 5000,
// 	                ],
// 	                'template' => '{list}',
// 	            ]),
// 	            'class' => 'yii\grid\ActionColumn',
// 	            'template' => '{update}',
//         	],     
        ],
    ]); ?>

</div>

<script>
    
$(document).ready(function() {

    //reinicjalizacja kalendarza z datami po użyciu pjax'a
    $("#installation-grid-pjax").on("pjax:complete", function() {
        
        if ($('#installationsearch-wire_date').data('kvDatepicker')) { $('#installationsearch-wire_date').kvDatepicker('destroy'); }
        $('#installationsearch-wire_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('installationsearch-wire_date');
        if ($('#installationsearch-socket_date').data('kvDatepicker')) { $('#installationsearch-socket_date').kvDatepicker('destroy'); }
        $('#installationsearch-socket_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('task-start');
    });
});

</script>
