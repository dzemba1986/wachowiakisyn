<?php

use yii\widgets\DetailView;
use yii\bootstrap\Collapse;
use yii\helpers\Html;

?>

<?php $vip = $modelConnection->vip == true ? "(VIP)" : null; ?>

<?php  echo '<center><h4>' . $modelConnection->modelAddress->fullAddress . ' ' . $vip . '</h4></center>'; ?>
    
<div style="width: 49%; display: inline-block;">
	<?= DetailView::widget([
    	'model' => $modelConnection,
        'formatter' => [
        	'class' => 'yii\i18n\Formatter',
        	'nullDisplay' => ''
        ],
        'attributes' => [
        	'id',             
            'soa_id',
            'phone',
            'phone2',
            'mac',
            //'port',
        ],
	]);
	?>
</div>
        
<div style="width: 49%; display: inline-block; float: right">
	<?= DetailView::widget([
    	'model' => $modelConnection,
       	'formatter' => [
        	'class' => 'yii\i18n\Formatter',
        	'nullDisplay' => ''
        ],
        'attributes' => [
            'start_date',
        	'soa_date',
            'conf_date',
            'pay_date',
            'close_date',
        ],
    ]);
    ?>
</div>
   
<?= DetailView::widget([
	'model' => $modelConnection,
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
    
<div class="installation">
	<?php 
	$installations = $modelConnection->modelInstallations; 
	$i = 0;
	$arIns = [];
	
	foreach ($installations as $installation){
		$arInstallations[$i]['label'] = $installation->modelType->name;
	    $arInstallations[$i]['content'] = $this->render('@app/views/installation/_view', ['model' => $installation]);
	    //var_dump($arInstallations);
	    //exit;
	    $i++;
	}
	 
	if(isset($arInstallations))
		echo Collapse::widget([
	    	'items' => $arInstallations,
		]); 
	?>
</div>