
<?php

/**
 * @var yii\web\View $this
 * @var common\models\soa\ConnectionSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use backend\modules\address\models\Teryt;
use common\models\soa\Package;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

echo $this->renderFile('@app/views/modal/modal.php');
echo $this->renderFile('@app/views/modal/modal_calendar.php');

$this->params['breadcrumbs'][] = 'LP';
$this->params['breadcrumbs'][] = 'Umowy otwarte';

echo GridView::widget([
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
	'resizableColumns' => false,
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
	'summary' => 'Widoczne {count} z {totalCount}',
	'panel' => [
	    'heading' => 'Otwarte',
	],
    'panelHeadingTemplate' => '{summary}{title}',
    'panelBeforeTemplate' => '',
    'panelAfterTemplate' => '{export}',
// 	'rowOptions' => function($model){
//     	if ($model->exec_from) return ['class' => 'sevendays'];
// 	},
	'columns' => [
        [
			'header' => 'Lp.',
			'class' => 'kartik\grid\SerialColumn',
           	'options' => ['style'=>'width: 4%;'],
            'mergeHeader' => true
		],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'value' => function ($model){

                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model){

                return 'Info: '.$model->desc . '<br>Info Boa: ' . $model->desc_boa;
            },
        ],
        [
        	'attribute' => 'start_at',
            'format' => ['date', 'php:Y-m-d'],
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
            	'model' => $searchModel,
            	'attribute' => 'start_at',
            	'pickerButton' => false,
            	'language' => 'pl',
            	'pluginOptions' => [
            		'format' => 'yyyy-mm-dd',
            		'todayHighlight' => true,
            		'endDate' => '0d'	
            	]
            ],
        ],
        [
            'attribute' => 'street',
            'value' => 'address.ulica',
            'filter' => ArrayHelper::map(Teryt::findOrderStreetName(), 't_ulica', 'ulica'),
        ],	
        [
            'attribute' => 'house',
            'value' => 'address.dom',
        ],
        [
            'attribute' => 'house_detail',
            'value' => 'address.dom_szczegol',
        ],
        [
            'attribute' => 'flat',
            'value' => 'address.lokal',
        ],
        [
            'attribute' => 'type_id',
            'value' => 'type.name',
            'filter' => ArrayHelper::map(Package::find()->where(['parent_id' => 0])->asArray()->all(), 'id', 'name'),
        ],
//         [
//             'attribute' => 'phone_desc',
//             'options' => ['style'=>'width:10%;'],
//             'visible' => Yii::$app->user->id <> 19 ? false : true
//         ],
        [
	        'attribute' => 'package_id',
            'value' => 'package.name',
            'filter' => ArrayHelper::map(Package::find()->select(['id', 'name'])->where(['<>', 'parent_id', 0])->asArray()->all(), 'id', 'name'),
        ],
//         [
//             'attribute' => 'wire',
//             'format' => 'raw',
//             'value'=> function($model, $key) {
//             if ($model->wire > 0) {
//                 return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-ok text-success']), ['test/test']);
//             } else
//                 return Html::a(Html::tag('span', 'dodaj', ['class' => 'label label-danger']), ['installation/create', 'connectionId' => $key], [
//                     'onclick' => "
//                         $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
                    
//                         return false;
//                     "
//                 ]);
//             },
//             'filter' => [1 => 'Tak', 0 => 'Nie'],
//             'options' => ['style' => 'width:8%;'],
//             'visible' => Yii::$app->user->identity->group == 2,
//         ],
//         [
//             'class' => 'kartik\grid\BooleanColumn',
//             'attribute' => 'socket',
//             'header' => 'Gniazdo',
//         	'trueLabel' => 'Tak',
//         	'falseLabel' => 'Nie',
//             'options' => ['style' => 'width:7%;'],
//             'vAlign' => 'middle'
//         ],
//         [
//         	'attribute' => 'task_id',
//         	'label' => 'Montaż',
//         	'format' => 'raw',	
//         	'value'=> function($model, $key) {
//         		if (!is_null($model->task_id)){
//         			if (is_object($model->task))
//         				return Html::a($model->task->day, ['/crm/task/calendar-ajax', 'connectionId' => $key], [
//         				    'onclick' => "
//                                 $('#modal-calendar').modal('show').find('#modal-calendar-content').load($(this).attr('href'));
            				    
//                                 return false;
//                             "
//         				]);
//         		}
//         		elseif ($model->socket > 0)
//         			return null;
//         		else
//         		    return Html::a(Html::tag('span', 'dodaj', ['class' => 'label label-danger']), ['/crm/task/calendar-ajax', 'connectionId' => $key], [
//         			    'onclick' => "
//                             $('#modal-calendar').modal('show').find('#modal-calendar-content').load($(this).attr('href'));
   
//                             return false;
//                         "
//         			]);
// 			},
// 			'filter' => false,
// 			'options' => ['style'=>'width:8%;'],
//         ],
//         [
//         	'attribute' => 'conf_date',
// 			'filterType' => GridView::FILTER_DATE,
// 			'filterWidgetOptions' => [
// 				'model' => $searchModel,
// 				'attribute' => 'conf_date',
// 				'pickerButton' => false,
// 				'language' => 'pl',
// 				'pluginOptions' => [
// 					'format' => 'yyyy-mm-dd',
// 					'todayHighlight' => true,
// 					'endDate' => '0d',
// 				]
// 			],
// 			'options' => ['style'=>'width:8%;'],
//             'headerOptions' => ['class' => 'skip-export'],
//             'contentOptions' => ['class' => 'skip-export'],
//             'visible' => Yii::$app->user->id <> 19 ? true : false
// 		],
// 		[
// 		    'attribute' => 'pay_date',
// 		    'filterType' => GridView::FILTER_DATE,
// 		    'filterWidgetOptions' => [
// 		        'model' => $searchModel,
// 		        'attribute' => 'pay_date',
// 		        'pickerButton' => false,
// 		        'language' => 'pl',
// 		        'pluginOptions' => [
// 		            'format' => 'yyyy-mm-dd',
// 		            'todayHighlight' => true,
// 		            'endDate' => '0d',
// 		        ]
// 		    ],
// 		    'options' => ['style'=>'width:8%;'],
// 		    'headerOptions' => ['class' => 'skip-export'],
// 		    'contentOptions' => ['class' => 'skip-export'],
// 		    'visible' => Yii::$app->user->id <> 19 ? true : false
// 		],
//         [   
//             'header' => PageSize::widget([
//                 'defaultPageSize' => 100,
//                 'pageSizeParam' => 'per-page',
//                 'sizes' => [
//                     10 => 10,
//                     25 => 25,
//                     50 => 50,
//                     100 => 100,
//                     500 => 500,
//                     1000 => 1000,
//                 ],
//                 'label' => 'Ilość',
//                 'template' => '{label}{list}',
//                 'options' => ['class' => 'form-control'],
//             ]),
//             'class' => 'kartik\grid\ActionColumn',
//             'dropdown' => true,
//             'dropdownButton' => ['label' => 'Akcje','class'=>'btn btn-secondary', 'style' => 'padding: 0px 5px'],
//             'mergeHeader' => true,
//             'template' => '{view} {update} {tree} {history}',
//             'options' => ['style'=>'width:6%;'],
//             'visibleButtons' => [
//                 'config' => function ($model) { return $model->canConfigure(); },
//                 'tree' => function ($model) { return $model->host; },
//             ],
//         	'buttons' => [
//         	    'view' => function($url, $model, $key) {
//             	    $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span> Podgląd', $url, [
//             	        'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
//             	    ]);
//                     return Html::tag('li', $link, []);  
//         	    },
//         	    'update' => function($url, $model, $key) {
//             	    $link = Html::a('<span class="glyphicon glyphicon-pencil"></span> Edycja', $url, [
//             	        'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
//             	    ]);
//                     return Html::tag('li', $link, []);  
//         	    },
//         		'config' => function ($url, $model, $key) {
//     			    $link = Html::a('<span class="glyphicon glyphicon-plus"></span> Konfiguracja', $model->addHostUrl, [
//         			    'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
//         			]);
//                     return Html::tag('li', $link, []);  
//         		},
//         		'tree' => function ($url, $model, $key) {
//     				$link = Html::a('<span class="glyphicon glyphicon-play"></span> SEU', $model->treeUrl, [
//     				    'target'=>'_blank',
//     				]);
//                     return Html::tag('li', $link, []);  
//         		},
//         		'history' => function ($url, $model, $key) {
//             		$url = Url::to(['/history/history/connection', 'id' => $key]);
//             		$link = Html::a('<span class="glyphicon glyphicon-menu-hamburger"></span> Historia', $url, [
//             		    'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
//             		]);
//                     return Html::tag('li', $link, []);
//         		}
//         	]
//         ],            
    ]
]); 
?>