<?php

/**
 * @var yii\web\View $this
 * @var $searchModel backend\modules\address\models\AddressSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use backend\modules\address\models\Teryt;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

echo $this->renderFile('@app/views/modal/modal.php');

echo GridView::widget([
    'id' => 'service-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
	'pjax' => true,
	'pjaxSettings' => [
		'options' => [
			'id' => 'service-grid-pjax'
		]
	],
    'summary' => false,
    'columns' => [
        [
        	'class' => SerialColumn::class,
            'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', ['create-service'], [
                'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
            ]),
        ],
        [
        	'attribute' => 'ulica_prefix',
    	    'headerOptions' => ['class' => 'vertical'],
    	    'filter' => false,
		],
        [
            'attribute' => 'ulica',
            'value' => 'ulica',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(Teryt::findOrderStreetName(), 't_ulica', 'ulica'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'ulica'],
            'format' => 'raw',
            'options' => ['style'=>'width:50%;']
        ],
    	'dom',
    	'dom_szczegol',
        'lokal_od',
        'lokal_do',
        [
            'attribute' => 'utp',
    	    'headerOptions' => ['class' => 'vertical group-infra'],
            'contentOptions' => ['class' => 'group-infra'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->utp == -1) return '<span class="label label-warning">todo</span>';
                elseif ($model->utp == 0) return '<span class="label label-danger">brak</span>';
                elseif ($model->utp == -2) return '<span class="label label-warning">wizja</span>';
                elseif ($model->utp > 0) return '<span class="label label-success">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'utp_cat3',
            'headerOptions' => ['class' => 'vertical group-infra'],
            'contentOptions' => ['class' => 'group-infra'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->utp_cat3 == -1) return '<span class="label label-warning">todo</span>';
                elseif ($model->utp_cat3 == 0) return '<span class="label label-danger">brak</span>';
                elseif ($model->utp_cat3 == -2) return '<span class="label label-warning">wizja</span>';
                elseif ($model->utp_cat3 > 0) return '<span class="label label-success">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'coax',
            'headerOptions' => ['class' => 'vertical group-infra'],
            'contentOptions' => ['class' => 'group-infra'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->coax == -1) return '<span class="label label-warning">todo</span>';
                elseif ($model->coax == 0) return '<span class="label label-danger">brak</span>';
                elseif ($model->coax == -2) return '<span class="label label-warning">wizja</span>';
                elseif ($model->coax > 0) return '<span class="label label-success">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'optical_fiber',
            'headerOptions' => ['class' => 'vertical group-infra'],
            'contentOptions' => ['class' => 'group-infra'],
            'format' => 'raw',
            'value' => function ($model) {
                if($model->optical_fiber == -1) return '<span class="label label-warning">todo</span>';
                elseif($model->optical_fiber == 0) return '<span class="label label-danger">brak</spapn>';
                elseif($model->optical_fiber == -2) return '<span class="label label-warning">wizja</pspan>';
                elseif ($model->optical_fiber > 0) return '<span class="label label-success">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'net_1g_utp',
            'headerOptions' => ['class' => 'vertical group-net'],
            'contentOptions' => ['class' => 'group-net'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->net_1g_utp == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->net_1g_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'net_1g_opt',
            'headerOptions' => ['class' => 'vertical group-net'],
            'contentOptions' => ['class' => 'group-net'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->net_1g_opt == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->net_1g_opt == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'net_10g_utp',
            'headerOptions' => ['class' => 'vertical group-net'],
            'contentOptions' => ['class' => 'group-net'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->net_10g_utp == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->net_10g_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'net_10g_opt',
            'headerOptions' => ['class' => 'vertical group-net'],
            'contentOptions' => ['class' => 'group-net'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->net_10g_opt == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->net_10g_opt == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'phone',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #fff0e6;'],
    	    'contentOptions' => ['style' => 'background: #fff0e6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->phone == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->phone == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'hfc',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffe6ff;'],
    	    'contentOptions' => ['style' => 'background: #ffe6ff;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->hfc == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->hfc == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_utp',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffe6ff;'],
    	    'contentOptions' => ['style' => 'background: #ffe6ff;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_utp == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->iptv_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_opt',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffe6ff;'],
    	    'contentOptions' => ['style' => 'background: #ffe6ff;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_opt == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->iptv_opt == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'rfog',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffe6ff;'],
    	    'contentOptions' => ['style' => 'background: #ffe6ff;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->rfog == 0) return '<span class="label label-danger">nie</span>';
                elseif ($model->rfog == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_net_1g_utp',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_net_1g_utp == 0) return '<span class="label label-danger">nie</span>';    
                elseif ($model->iptv_net_1g_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_net_1g_opt',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_net_1g_opt == 0) return '<span class="label label-danger">nie</span>';    
                elseif ($model->iptv_net_1g_opt == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_net_10g_utp',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_net_10g_utp == 0) return '<span class="label label-danger">nie</span>';    
                elseif ($model->iptv_net_10g_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_net_10g_opt',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_net_10g_opt == 0) return '<span class="label label-danger">nie</span>';    
                elseif ($model->iptv_net_10g_opt == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'rfog_net_1g',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->rfog_net_1g == 0) return '<span class="label label-danger">nie</span>';    
                elseif ($model->rfog_net_1g == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'rfog_net_10g',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->rfog_net_10g == 0) return '<span class="label label-danger">nie</span>';    
                elseif ($model->rfog_net_10g == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'class' => ActionColumn::class,
            'header' => 'Akcje',
            'mergeHeader' => true,
            'template' => '{update}',
            'buttons' => [
                'update' => function($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'onclick' => "$('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href')); return false;"
                    ]);
                },
            ]
        ],
    ]
]);