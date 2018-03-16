<?php

use yii\bootstrap\Collapse;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var backend\models\Connection $model
 */
?>
 
<div class="col-sm-6" style="padding-left:0">

    <?= DetailView::widget([
        'model' => $model,
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => ''
        ],
        'attributes' => [
            'id',
            'soa_id',
            'phone',
            'phone2',
            [
                'attribute' => 'mac',
                'visible' => in_array($model->type_id, [1,3]),
            ],
            [
                'attribute' => 'vip',
                'value' => $model->vip ? 'Tak' : 'Nie',
                'contentOptions' => $model->vip ? ['style' => 'color: red'] : [],
            ]
        ],
    ]);
    ?>
    
</div>
        
<div class="col-sm-6" style="padding-right:0">

	<?= DetailView::widget([
    	'model' => $model,
       	'formatter' => [
        	'class' => 'yii\i18n\Formatter',
        	'nullDisplay' => ''
        ],
        'attributes' => [
            [
                'attribute' => 'start_date',
                'format' => ['date', 'php:Y-m-d'],
            ],
        	'soa_date',
            [
                'attribute' => 'conf_date',
                'visible' => in_array($model->type_id, [1,3]),
            ],
            [
                'attribute' => 'phone_date',
                'visible' => in_array($model->type_id, [2]),
            ],
            'pay_date',
            [
                'attribute' => 'close_date',
                'format' => ['date', 'php:Y-m-d'],
                'contentOptions' => ['style' => 'color: red'],
            ]
        ],
    ]);
    ?>
    
</div>

<?= DetailView::widget([
	'model' => $model,
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => ''
	],
	'attributes' => [
		'info',
		'info_boa'
	],
]);
?>

<?= Html::label('<h4>Instalacje : </h4>') ?> 
    
	<?php 
	$i = 0;
	$arIns = [];
	
	foreach ($installations as $installation){
		$arInstallations[$i]['label'] = $installation->type->name;
	    $arInstallations[$i]['content'] = $this->render('@app/views/installation/_view', ['model' => $installation]);
	    $i++;
	}
	 
	if(isset($arInstallations))
		echo Collapse::widget([
	    	'items' => $arInstallations,
		]); 
	?>

<?php 
$this->registerJs(
"$(function(){
	$('.modal-header h4').html('{$model->address->toString()}');
});"
);
?>