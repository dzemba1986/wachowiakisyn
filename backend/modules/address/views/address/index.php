<?php

/**
 * @var $this yii\web\View
 * @var $searchModel backend\models\AddressShortSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use backend\modules\address\models\Teryt;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\SerialColumn;

$this->title = 'Obsługiwane ulice';
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@app/views/modal/modal_sm.php');

echo Html::beginTag('div', ['class' => 'col-md-4']);

    echo GridView::widget([
        'id' => 'street-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    	'pjax' => true,
    	'pjaxSettings' => [
    		'options' => [
    			'id' => 'street-grid-pjax'
    		]
    	],
    	'summary' => false,
        'columns' => [
            [
            	'class' => SerialColumn::class,
                'mergeHeader' => true,
            	'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', ['create-street'], [
            	    'onclick' => "$('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href')); return false;"
            	]),
            ],
            [
                'attribute' => 'ulica_prefix',
                'filter' => ['ul.' => 'ul.', 'os.' => 'os.'],
                'options' => ['style'=>'width:5%'],
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
            'name',
            [
            	'class' => ActionColumn::class,
                'header' => 'Akcje',
                'mergeHeader' => true,
            	'template' => '{view} {update}',
                'dropdown' => true,
                'dropdownMenu' => ['class'=>'text-left', 'style' => 'left: -100px;'],
                'dropdownButton' => ['label' => 'Akcje','class'=>'btn btn-secondary', 'style' => 'padding: 0px 5px'],
            	'buttons' => [
            	    'view' => function($url, $model, $key) {
            	        $url = Url::to(['address-by-street', 't_ulica' => $model->t_ulica]);
                	    $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span> Adresy', $url, [
                	        'onclick' => "$('#address').load($(this).attr('href')); return false;"
                	    ]);
                	    return Html::tag('li', $link);
            	    },
            	    'update' => function($url, $model, $key) {
            	        $url = Url::to(['update-teryt', 'id' => trim($model->t_ulica)]);
                	    $link = Html::a('<span class="glyphicon glyphicon-pencil"></span> Edycja', $url, [
						    'onclick' => "$('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href')); return false;"
                	    ]);
                	    return Html::tag('li', $link);
            	    },
        		]	
        	]
        ]
    ]);

echo Html::endTag('div');
echo Html::tag('div', '', ['id' => 'address', 'class' => 'col-md-8']);
?>