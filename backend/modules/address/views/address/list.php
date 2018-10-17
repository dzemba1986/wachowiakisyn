<?php

/**
 * @var $this yii\web\View
 * @var $searchModel backend\models\AddressShortSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use backend\modules\address\models\AddressShort;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Ulice';
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@backend/views/modal/modal_sm.php');

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
            	'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['create']), ['class' => 'create-button']),
            ],
            [
                'attribute' => 'ulica_prefix',
                'options' => ['style'=>'width:5%'],
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'ulica_prefix',
                    ArrayHelper::map(
                        AddressShort::findGroupByPrefix(),
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
                'filter' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'ulica'],
                'format' => 'raw',
                'options' => ['style'=>'width:20%;']
            ],
            'name',
        	't_miasto',	
        	't_woj',
        	't_pow',
        	't_gmi',
        	't_rodz',
        	't_ulica',	
            [
            	'class' => 'yii\grid\ActionColumn',
            	'template' => '{update}',
            	'buttons' => [
					'update' => function ($url, $model){
						return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['update-short', 'id' => trim($model->t_ulica)]), [
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
        
			$('#modal-sm').modal('show')
				.find('#modal-sm-content')
				.load($(this).attr('href'));
	
	        return false;
		})
		.on('click', '.create-button', function(event){
        
			$('#modal-sm').modal('show')
				.find('#modal-sm-content')
				.load($(this).attr('href'));
	
	        return false;
		})
});
JS;
$this->registerJs($js);
?>