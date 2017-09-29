<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use backend\models\Address;
use kartik\select2\Select2;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $searchModel backend\models\AddressSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Adresy';
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
            		ArrayHelper::map(
            			Address::find()->select('ulica_prefix')->groupBy('ulica_prefix')->all(), 
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
        		'filter' => ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica'),
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
            [
            	'class' => 'yii\grid\ActionColumn',
            	'template' => '{update}',
            	'buttons' => [
					'update' => function ($url){
						return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
							'title' => \Yii::t('yii', 'Edycja'),
// 							'data-pjax' => '0',
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
        
    $('body').on('click', '.update-button', function(event){
        
		$('#modal-update-address').modal('show')
			.find('#modal-content-calendar')
			.load($(this).attr('href'));

        return false;
	});
});
JS;
$this->registerJs($js);
?>