<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use backend\models\Address;
use yii\base\Widget;
use yii\helpers\Url;
use backend\models\AddressShort;

/**
 * @var $this yii\web\View
 * @var $searchModel backend\models\AddressShortSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Ulice';
$this->params['breadcrumbs'][] = $this->title;

require_once '_modal_update.php';

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
    	'summary' => 'Widoczne {count} z {totalCount}',
        'columns' => [
            [
            	'class' => 'yii\grid\SerialColumn',
            	'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['address/create']), ['class' => 'create-button']),
            ],
        	't_miasto',	
        	't_woj',
        	't_pow',
        	't_gmi',
        	't_rodz',
        	't_ulica',	
            [
            	'attribute' => 'ulica_prefix',
            	'options' => ['style'=>'width:5%'],
            	'filter' => Html::activeDropDownList(
            		$searchModel, 
            		'ulica_prefix', 
            		ArrayHelper::map(
            			AddressShort::find()->select('ulica_prefix')->groupBy('ulica_prefix')->all(), 
            			'ulica_prefix', 
            			'ulica_prefix'
            		), 
            		['prompt'=>'', 'class'=>'form-control']
            	),	
    		],
        	[
        		'attribute' => 'ulica',
        		'value' => 'ulica',
        		'filterType' => GridView::FILTER_SELECT2,
        		'filter' => ArrayHelper::map(AddressShort::find()->select('ulica')->all(), 'ulica', 'ulica'),
        		'filterWidgetOptions' => [
        			'pluginOptions' => ['allowClear' => true],
        		],
        		'filterInputOptions' => ['placeholder' => 'ulica'],
        		'format' => 'raw',
        		'options' => ['style'=>'width:20%;']
        	],
        	'name',	
            [
            	'class' => 'yii\grid\ActionColumn',
            	'template' => '{update}',
            	'buttons' => [
					'update' => function ($url, $model){
						return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['address/update-short', 'id' => trim($model->t_ulica)]), [
							'title' => \Yii::t('yii', 'Edycja'),
							'class' => ['update-button']	
						]);
        			}
        		]	
        	]
        ]
    ]); ?>

</div>

<?php 
$js = <<<JS
$(document).ready(function() {
        
    $('body')
		.on('click', '.update-button', function(event){
        
			$('#modal-update').modal('show')
				.find('#modal-content')
				.load($(this).attr('href'));
	
	        return false;
		})
		.on('click', '.create-button', function(event){
        
			$('#modal-update').modal('show')
				.find('#modal-content')
				.load($(this).attr('href'));
	
	        return false;
		})
});
JS;
$this->registerJs($js);
?>