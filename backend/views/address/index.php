<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use backend\models\Address;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Adresy';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
            	'class' => 'yii\grid\SerialColumn',
            	'options' => ['style'=>'width:5%;']	
            ],
            [
            	'attribute' => 'ulica_prefix',
            	'options' => ['style'=>'width:5%'],
            	'filter' => Html::activeDropDownList(
            		$searchModel, 
            		'ulica_prefix', 
            		ArrayHelper::map(
            			Address::find()->select('ulica_prefix')->groupBy('ulica_prefix')->all(), 
            			'ulica_prefix', 
            			'ulica_prefix'
            		), 
            		['prompt'=>'', 'class'=>'form-control']
            	),	
    		],
        	[
        		'attribute'=>'ulica',
        		'filter'=> Html::activeDropDownList($searchModel, 'ulica', ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica'), ['prompt'=>'', 'class'=>'form-control']),
        		'options' => ['style'=>'width:15%;'],
        	],
            [
            	'attribute' => 'dom',
            	'options' => ['style'=>'width:5%;']
    		],
        	[	
            	'attribute' => 'dom_szczegol',
        		'options' => ['style'=>'width:5%;']
        	],		
            [
            	'attribute' => 'lokal',
            	'options' => ['style'=>'width:5%;']
    		],
        	[
        		'attribute' => 'pietro',
        		'options' => ['style'=>'width:5%;']
        	],        		
            'lokal_szczegol',
            [
            	'class' => 'yii\grid\ActionColumn',
            	'options' => ['style'=>'width:5%;']
        	],
        ],
    ]); ?>

</div>
