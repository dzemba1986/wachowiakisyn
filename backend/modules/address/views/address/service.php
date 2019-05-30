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
                if ($model->utp == -1) return '<span class="label label-danger">todo</span>';
                if ($model->utp == 0) return '<span class="label label-danger">brak</span>';
                else return $model->utp;
            },
            'filter' => false,
        ],
        [
            'attribute' => 'utp_cat3',
            'headerOptions' => ['class' => 'vertical group-infra'],
            'contentOptions' => ['class' => 'group-infra'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->utp_cat3 == -1) return '<span class="label label-danger">todo</span>';
                if ($model->utp_cat3 == 0) return '<span class="label label-danger">brak</span>';
                else return $model->utp_cat3;
            },
            'filter' => false,
        ],
        [
            'attribute' => 'coax',
            'headerOptions' => ['class' => 'vertical group-infra'],
            'contentOptions' => ['class' => 'group-infra'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->coax == -1) return '<span class="label label-danger">todo</span>';
                if ($model->coax == 0) return '<span class="label label-danger">brak</span>';
                else return $model->coax;
            },
            'filter' => false,
        ],
        [
            'attribute' => 'optical_fiber',
            'headerOptions' => ['class' => 'vertical group-infra'],
            'contentOptions' => ['class' => 'group-infra'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->optical_fiber == -1) return '<span class="label label-danger">todo</span>';
                if ($model->optical_fiber == 0) return '<span class="label label-danger">brak</span>';
                else return $model->optical_fiber;
            },
            'filter' => false,
        ],
        [
            'attribute' => 'net_utp',
            'headerOptions' => ['class' => 'vertical group-net'],
            'contentOptions' => ['class' => 'group-net'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->net_utp == 0) return '<span class="label label-danger">nie</span>';
                if ($model->net_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'net_optical_fiber',
            'headerOptions' => ['class' => 'vertical group-net'],
            'contentOptions' => ['class' => 'group-net'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->net_optical_fiber == 0) return '<span class="label label-danger">nie</span>';
                if ($model->net_optical_fiber == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'netx_utp',
            'headerOptions' => ['class' => 'vertical group-net'],
            'contentOptions' => ['class' => 'group-net'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->netx_utp == 0) return '<span class="label label-danger">nie</span>';
                if ($model->netx_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'netx_optical_fiber',
            'headerOptions' => ['class' => 'vertical group-net'],
            'contentOptions' => ['class' => 'group-net'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->netx_optical_fiber == 0) return '<span class="label label-danger">nie</span>';
                if ($model->netx_optical_fiber == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'phone_utp',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #fff0e6;'],
    	    'contentOptions' => ['style' => 'background: #fff0e6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->phone_utp == 0) return '<span class="label label-danger">nie</span>';
                if ($model->phone_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'phone_utp_cat3',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #fff0e6;'],
    	    'contentOptions' => ['style' => 'background: #fff0e6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->phone_utp_cat3 == 0) return '<span class="label label-danger">nie</span>';
                if ($model->phone_utp_cat3 == 1) return '<span class="label label-success">tak</span>';
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
                if ($model->hfc == 1) return '<span class="label label-success">tak</span>';
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
                if ($model->iptv_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_optical_fiber',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffe6ff;'],
    	    'contentOptions' => ['style' => 'background: #ffe6ff;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_optical_fiber == 0) return '<span class="label label-danger">nie</span>';
                if ($model->iptv_optical_fiber == 1) return '<span class="label label-success">tak</span>';
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
                if ($model->rfog == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_net_utp',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_net_utp == 0) return '<span class="label label-danger">nie</span>';    
                if ($model->iptv_net_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_net_optical_fiber',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_net_optical_fiber == 0) return '<span class="label label-danger">nie</span>';    
                if ($model->iptv_net_optical_fiber == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_netx_utp',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_netx_utp == 0) return '<span class="label label-danger">nie</span>';    
                if ($model->iptv_netx_utp == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'iptv_netx_optical_fiber',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->iptv_netx_optical_fiber == 0) return '<span class="label label-danger">nie</span>';    
                if ($model->iptv_netx_optical_fiber == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'rfog_net',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->rfog_net == 0) return '<span class="label label-danger">nie</span>';    
                if ($model->rfog_net == 1) return '<span class="label label-success">tak</span>';
                else return '<span class="label label-warning">tak</span>';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'rfog_netx',
    	    'headerOptions' => ['class' => 'vertical', 'style' => 'background: #ffffe6;'],
    	    'contentOptions' => ['style' => 'background: #ffffe6;'],
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->rfog_netx == 0) return '<span class="label label-danger">nie</span>';    
                if ($model->rfog_netx == 1) return '<span class="label label-success">tak</span>';
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