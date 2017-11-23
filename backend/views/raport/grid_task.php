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

$this->title = 'Zestawienia';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Montaże';
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
                'id' => 'task-grid-pjax'
            ]    
        ],
        'resizableColumns' => FALSE,
        'formatter' => [
			'class' => 'yii\i18n\Formatter',
			'nullDisplay' => ''
		],
    	'summary' => 'Widoczne {count} z {totalCount}',
    	'export'=>[
	    	'fontAwesome'=>true,
	        'showConfirmAlert'=>false,
	        'target'=>GridView::TARGET_BLANK,
	        'exportConfig' => ['pdf' => TRUE, 'json' => FALSE],
    	],
        'panel' => [
        	'heading'=> '',
			'before' => $this->render('_search_task', [
					'searchModel' => $searchModel,
			]),
	],
        'columns' => [
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
                'class'=>'kartik\grid\SerialColumn',
            	'pageSummary' => 'Łącznie',
                'options'=>['style'=>'width: 4%;'],
            ],
        	[
        		'attribute' => 'start',
        		'value'=> function ($model){
        			return date("Y-m-d", strtotime($model->start));
        		},
        		'filterType' => GridView::FILTER_DATE,
        		'filterWidgetOptions' => [
        			'model' => $searchModel,
        			'attribute' => 'create',
        			'pickerButton' => false,
        			//'removeButton' => false,
        			'language' => 'pl',
        			'pluginOptions' => [
        				'format' => 'yyyy-mm-dd',
        				'todayHighlight' => true,
        			]
        		],
        		'options' => ['id'=>'start', 'style'=>'width:10%;'],
        	],
            [	
                'attribute'=>'street',
                'value'=>'address.ulica',
                'filter'=> Html::activeDropDownList($searchModel, 'street', ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->orderBy('ulica')->all(), 'ulica', 'ulica'), ['prompt'=>'', 'class'=>'form-control']),
                'options' => ['style'=>'width:12%;'],
            ],	
            [
                'attribute'=>'house',
                'value'=>'address.dom',
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'house_detail',
                'value'=>'address.dom_szczegol',
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'flat',
                'value'=>'address.lokal',
                'options' => ['style'=>'width:5%;'],
            ],
            [
                'attribute'=>'type',
                'value'=>'type.name',
                'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(Type::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
                'options' => ['style'=>'width:5%;'],
            ],
            [
            	'attribute' => 'close',
            	'value'=> function ($model){
            		return date("Y-m-d", strtotime($model->close));
            	},
            	'filterType' => GridView::FILTER_DATE,
            	'filterWidgetOptions' => [
            		'model' => $searchModel,
            		'attribute' => 'close',
            		'pickerButton' => false,
            		//'removeButton' => false,
            		'language' => 'pl',
            		'pluginOptions' => [
            			'format' => 'yyyy-mm-dd',
            			'todayHighlight' => true,
            		]
            	],
            	'options' => ['id'=>'start', 'style'=>'width:10%;'],
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
        
    	if (jQuery('#installationsearch-minsocketdate').data('kvDatepicker')) { jQuery('#installationsearch-minsocketdate').kvDatepicker('destroy'); }
    	jQuery('#installationsearch-minsocketdate-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPRemove('installationsearch-minsocketdate');
    	initDPAddon('installationsearch-minsocketdate');
    	if (jQuery('#installationsearch-maxsocketdate').data('kvDatepicker')) { jQuery('#installationsearch-maxsocketdate').kvDatepicker('destroy'); }
    	jQuery('#installationsearch-maxsocketdate-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPRemove('installationsearch-maxsocketdate');
    	initDPAddon('installationsearch-maxsocketdate');
    	jQuery('#global-search').yiiActiveForm([], []);
    	if (jQuery('#installationsearch-wire_date').data('kvDatepicker')) { jQuery('#installationsearch-wire_date').kvDatepicker('destroy'); }
    	jQuery('#installationsearch-wire_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPAddon('installationsearch-wire_date');
    	if (jQuery('#installationsearch-socket_date').data('kvDatepicker')) { jQuery('#installationsearch-socket_date').kvDatepicker('destroy'); }
    	jQuery('#installationsearch-socket_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPAddon('installationsearch-socket_date');
    });
});

</script>