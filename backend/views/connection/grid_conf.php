<?php 
use kartik\grid\GridView;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\Address;
use backend\models\Type;
use nterms\pagesize\PageSize;
use yii\helpers\Url;
use backend\models\Device;

$this->params['breadcrumbs'][] = 'Do konfiguracji';
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
            'options' => ['id'=>'start', 'style'=>'width:8%;'],           
        ],	
       	[	
           	'attribute'=>'street',
           	'value'=>'modelAddress.ulica',
           	'filter'=> Html::activeDropDownList($searchModel, 'street', ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->orderBy('ulica')->all(), 'ulica', 'ulica'), ['prompt'=>'', 'class'=>'form-control']),
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
            'template' => '{view} {update} {tree}',
        	'buttons' => [
        		'tree' => function ($model, $data) {
        			if($data->mac && $data->port >= 0 && $data->device && !$data->nocontract && !$data->host && $data->wire > 0 && is_null($data->close_date)){
        				$url = Url::toRoute(['tree/add', 'id' => $data->id, 'host' => true]);
	        			return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
	        				'title' => \Yii::t('yii', 'Zamontuj'),
	        				'data-pjax' => '0',
	        			]);
        			} elseif ($data->host) {
        				$url = Url::toRoute(['tree/index', 'id' => $data->host . '.0']);
        				return Html::a('<span class="glyphicon glyphicon-play"></span>', $url, [
        					'title' => \Yii::t('yii', 'SEU'),
        					'data-pjax' => '0',
        				]);
        			} else
        				return null;
        		},
        	]
        ],                 
   ]
]); 
?>