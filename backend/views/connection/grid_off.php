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

$this->params['breadcrumbs'][] = 'Odłączone';
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
	'resizableColumns' => FALSE,
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
            'options' => ['id'=>'start', 'style'=>'width:8%;'],
            
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
            'class'=>'kartik\grid\BooleanColumn',
            'attribute'=>'nocontract',
            'trueLabel' => 'Tak', 
            'falseLabel' => 'Nie',
            //'value'=>'modelType.name',
            //'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(Type::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
            'options' => ['style'=>'width:5%;'],
        ],            
//        [
//            'header' => 'Kabel',
//            'format' => 'raw',
//            'value' => function($data){
//                if (sizeof($data->modelInstallationByType)  == 1)
//                    return ArrayHelper::getValue($data->modelInstallationByType, '0.attributes.wire_date');
//                elseif (sizeof($data->modelInstallationByType)  > 1){
//                    $i = 0;
//                    foreach ($data->modelInstallationByType as $objInstallation){
//                        $arInstallation[$i] = ArrayHelper::getValue($data->modelInstallationByType, $i.'.attributes.wire_date');
//                        $i++;
//                    }
//                    return Html::dropDownList('installation', 'id', $arInstallation, ['class'=>'form-control']);
//                }	
//                else 
//                    //return Html::a('dodaj', '', ['id'=>'link_create_installation',]);
//            return Html::a('dodaj', Url::to(['installation/wire-create', 'connectionId' => $data->id]), ['class'=>'button_create_installation']);
//            },
//            'options' => ['style'=>'width:7%;'],
//        ],
//        [
//            'header' => 'Kabel',
//            'format' => 'raw',
//            'value' => function($data){
//                if ($data->modelInstallationByType)
//                    return 'OK';
//                else 
//                    //return Html::a('dodaj', '', ['id'=>'link_create_installation',]);
//            return Html::a('dodaj', Url::to(['installation/wire-create', 'connectionId' => $data->id]), ['class'=>'button_create_installation']);
//            },
//            'options' => ['style'=>'width:7%;'],
//        ],            
//        [
//            'attribute' => 'socketDate', // it can be 'attribute' => 'tableField' to.
//            'header' => 'Gniazdo',
//            'format' => 'raw',
//            'value' => function($data) {
//                if(sizeof($data->modelInstallationByType) > 0){
//                    $i=0;
//                    $noSocket = 0;
//                    foreach ($data->modelInstallationByType as $installation){
//                        $arInstallation[$i] = $installation->attributes;
//
//                        if($arInstallation[$i]['socket_date'] == null)
//                            $noSocket++;
//                            $i++;
//                    }
//                    if(sizeof($data->modelInstallationByType) == $noSocket)
//                        return 'brak';
//                    elseif(sizeof($data->modelInstallationByType) == 1)
//                        return $arInstallation[0]['socket_date'];
//                    else
//                        return Html::dropDownList('ins', 'id', ArrayHelper::map($arInstallation, 'id', 'socket_date'), ['class'=>'form-control']);
//                }
//                else
//                    return 'brak';
//            },
//            'options' => ['style'=>'width:7%;'],
//        ],
        [
            'class'=>'kartik\grid\BooleanColumn',
            'attribute' => 'socketDate', // it can be 'attribute' => 'tableField' to.
            'header' => 'Gniazdo',
            //'format' => 'raw',
            'value' => 'socket',
//            'value' => function($data) {
//                if($data->modelInstallationByType <> NULL){
//                    
//                    foreach ($data->modelInstallationByType as $installation){
//                        
//                        if (isset($installation->socket_date))
//                            return TRUE;
//                        else
//                            return FALSE;
//                    }
//                }    
//                else
//                    return FALSE;
//            },
            'options' => ['style'=>'width:7%;'],
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
            'options' => ['style'=>'width:7%;'],
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
            'options' => ['style'=>'width:7%;'],
        ],
		[
		'attribute'=>'close_date',
		'value'=>'close_date',
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
		'options' => ['style'=>'width:7%;'],
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