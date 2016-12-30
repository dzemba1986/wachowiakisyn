<?php 
use kartik\grid\GridView;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\Address;
use backend\models\Type;
use nterms\pagesize\PageSize;

$this->title = 'Zestawienia';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Podłączenia';
?>

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
	'formatter' => [
			'class' => 'yii\i18n\Formatter',
			'nullDisplay' => ''
	],
	'resizableColumns' => FALSE,
	//'showPageSummary' => TRUE,
	'export'=>[
    	'fontAwesome'=>true,
        'showConfirmAlert'=>false,
        'target'=>GridView::TARGET_BLANK,
        'exportConfig' => ['pdf' => TRUE, 'json' => FALSE],
    ],
	'panel' => [
			'heading'=> '',
			'before' => $this->render('_search_connection', [
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
			'class'=>'yii\grid\SerialColumn',
           	//'options'=>['style'=>'width: 5%;'],
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
            //'options' => ['id'=>'start', 'style'=>'width:8%;'],
            
        ],	
        [	
            'attribute'=>'street',
            'value'=>'modelAddress.ulica',
            'filter'=> Html::activeDropDownList($searchModel, 'street', ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica'), ['prompt'=>'', 'class'=>'form-control']),
            //'options' => ['style'=>'width:12%;'],
        ],	
        [
            'attribute'=>'house',
            'value'=>'modelAddress.dom',
            'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute'=>'house_detail',
            'value'=>'modelAddress.dom_szczegol',
            //'options' => ['style'=>'width:5%;'],
        ],
        [
            'attribute'=>'flat',
            'value'=>'modelAddress.lokal',
            //'options' => ['style'=>'width:5%;'],
        ],
//        [
//            'attribute'=>'flat_detail',
//            'value'=>'modelAddress.lokal_szczegol',
//            'options' => ['style'=>'width:10%;'],
//        ],
        [
            'attribute'=>'type',
            'value'=>'modelType.name',
            'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(Type::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
            //'options' => ['style'=>'width:5%;'],
        ],
//         [
//             'class'=>'kartik\grid\BooleanColumn',
//             'attribute'=>'nocontract',
//             'trueLabel' => 'Tak', 
//             'falseLabel' => 'Nie',
//             'options' => ['style'=>'width:5%;'],
//         ],
		[
        	'class'=>'kartik\grid\BooleanColumn',
            'attribute'=>'again',
            'trueLabel' => 'Tak',
            'falseLabel' => 'Nie',
            //'options' => ['style'=>'width:5%;'],
        ],
        [
            'class'=>'kartik\grid\BooleanColumn',
            'attribute' => 'socket',
            'header' => 'Gniazdo',
        	'trueLabel' => 'Tak',
        	'falseLabel' => 'Nie',
            //'options' => ['style'=>'width:7%;'],
        ],                       
        [
            'attribute'=>'conf_date',
            'value'=>'conf_date',
            'format'=>'raw',
            'filter'=>	DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'conf_date',
                'removeButton' => FALSE,
                'language'=>'pl',	
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d', //wybór daty max do dziś
                ]
            ]),
            //'options' => ['style'=>'width:7%;'],
        ],
        [
            'attribute'=>'pay_date',
            'value'=>'pay_date',
            'format'=>'raw',
            'filter'=>	DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'pay_date',
                'removeButton' => FALSE,
                'language'=>'pl',	
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d', //wybór daty max do dziś
                ]
            ]),
            //'options' => ['style'=>'width:7%;'],
        ],
//         [   
//             'header' => PageSize::widget([
//                 'defaultPageSize' => 100,
//                 'pageSizeParam' => 'per-page',
//                 'sizes' => [
//                     10 => 10,
//                     100 => 100,
//                     500 => 500,
//                     1000 => 1000,
//                     //5000 => 5000,
//                 ],
//                 'template' => '{list}',
//             ]),
//             'class' => 'yii\grid\ActionColumn',
//             'template' => '{view} {update}',
//         ],            
    ]
		]); 
?>

<script>
    
$(document).ready(function() {
    
    //reinicjalizacja kalendarza z datami po użyciu pjax'a
    $("#connection-grid-pjax").on("pjax:complete", function() {
        
    	if (jQuery('#connectionsearch-minconfdate').data('kvDatepicker')) { jQuery('#connectionsearch-minconfdate').kvDatepicker('destroy'); }
    	jQuery('#connectionsearch-minconfdate-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPRemove('connectionsearch-minconfdate');
    	initDPAddon('connectionsearch-minconfdate');
    	if (jQuery('#connectionsearch-maxconfdate').data('kvDatepicker')) { jQuery('#connectionsearch-maxconfdate').kvDatepicker('destroy'); }
    	jQuery('#connectionsearch-maxconfdate-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPRemove('connectionsearch-maxconfdate');
    	initDPAddon('connectionsearch-maxconfdate');
    	jQuery('#global-search').yiiActiveForm([], []);
    	if (jQuery('#connectionsearch-start_date').data('kvDatepicker')) { jQuery('#connectionsearch-start_date').kvDatepicker('destroy'); }
    	jQuery('#connectionsearch-start_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPAddon('connectionsearch-start_date');
    	if (jQuery('#connectionsearch-conf_date').data('kvDatepicker')) { jQuery('#connectionsearch-conf_date').kvDatepicker('destroy'); }
    	jQuery('#connectionsearch-conf_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPAddon('connectionsearch-conf_date');
    	if (jQuery('#connectionsearch-pay_date').data('kvDatepicker')) { jQuery('#connectionsearch-pay_date').kvDatepicker('destroy'); }
    	jQuery('#connectionsearch-pay_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

    	initDPAddon('connectionsearch-pay_date');
    });
});

</script>