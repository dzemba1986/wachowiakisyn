<?php

use backend\models\AddressShort;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $searchModel backend\models\AddressSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Adresy';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="address-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    	'pjax' => true,
    	'pjaxSettings' => [
    		'options' => [
    			'id' => 'address-grid-pjax'
    		]
    	],
        'columns' => [
            [
            	'class' => 'yii\grid\SerialColumn',
            ],
        	'id', //TODO kolumna powinna być dostępna tylko dla administratora	
            [
            	'attribute' => 'ulica_prefix',
            	'options' => ['style'=>'width:5%'],
            	'filter' => Html::activeDropDownList(
            		$searchModel, 
            		'ulica_prefix', 
            		ArrayHelper::map(AddressShort::findGroupByPrefix(), 'ulica_prefix', 'ulica_prefix'), 
            		['prompt'=>'', 'class'=>'form-control']
            	),	
    		],
        	[
        		'attribute' => 'ulica',
        		'value' => 'ulica',
        		'filterType' => GridView::FILTER_SELECT2,
        		'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 'ulica', 'ulica'),
        		'filterWidgetOptions' => [
        			'pluginOptions' => ['allowClear' => true],
        		],
        		'filterInputOptions' => ['placeholder' => 'ulica'],
        		'format' => 'raw',
        		'options' => ['style'=>'width:20%;']
        	],
            'dom',
        	'dom_szczegol',	
            'lokal',
        	'pietro',        		
            'lokal_szczegol',
        ]
    ]); ?>

</div>