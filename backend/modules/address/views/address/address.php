<?php

/**
 * @var yii\web\View $this
 * @var $searchModel backend\modules\address\models\AddressSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\grid\ActionColumn;

echo $this->renderFile('@app/views/modal/modal.php');

$this->title = 'Adresy';
$this->params['breadcrumbs'][] = $this->title;

echo GridView::widget([
    'id' => 'address-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
	'pjax' => true,
	'pjaxSettings' => [
		'options' => [
			'id' => 'address-grid-pjax'
		]
	],
    'summary' => false,
    'columns' => [
        [
        	'class' => 'yii\grid\SerialColumn',
        ],
    	'id',
        [
        	'attribute' => 'ulica_prefix',
    	    'filter' => false,
        	'options' => ['style'=>'width:5%'],
		],
    	[
    		'attribute' => 'ulica',
    	    'filter' => false,
    		'options' => ['style'=>'width:20%;']
    	],
        'dom',
    	'dom_szczegol',	
        'lokal',
        'lokal_szczegol',
        'pietro',
        [
            'class' => ActionColumn::class,
            'header' => 'Akcje',
            'mergeHeader' => true,
            'template' => '{view}',
            'dropdown' => true,
            'dropdownMenu' => ['style' => 'left: -100px;'],
            'dropdownButton' => ['label' => 'Akcje','class'=>'btn btn-secondary', 'style' => 'padding: 0px 5px'],
            'buttons' => [
                'view' => function($url, $model, $key) {
                    $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span> Podgląd', $url, [
                        'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
                    ]);
                    return Html::tag('li', $link, []);
                },
//                 'update' => function($url, $model, $key) {
//                     $link = Html::a('<span class="glyphicon glyphicon-pencil"></span> Edycja', $url, [
//                         'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
//                     ]);
//                     return Html::tag('li', $link, []);
//                 },
            ]
        ],
    ]
]);