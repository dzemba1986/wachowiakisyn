<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Collapse;
use yii\helpers\ArrayHelper;
use backend\models\Device;

/* @var $this yii\web\View */
/* @var $model backend\models\Connection */

?>

<div class="connection-view">
    
    <?php  echo '<center><h4>'.$modelConnection->modelAddress->fullAddress.'</h4></center>'; ?>
    
    <div style="width: 49%; display: inline-block;">
        <?= DetailView::widget([
            'model' => $modelConnection,
//            'options' => [
//                'class' => 'table table-striped table-bordered detail-view detailviewconnection',
//            ],
        	'formatter' => [
        		'class' => 'yii\i18n\Formatter',
        		'nullDisplay' => ''
        	],
            'attributes' => [
                //'id',             
                'ara_id',
                'phone',
                'phone2',
                [
                'label' => 'Mac',
                'value' => $modelConnection->mac != NULL ?  $modelConnection->mac : ''
                ],
                'port',

            ],
        ]);
        ?>
    </div>
        
    <div style="width: 49%; display: inline-block; float: right">
        <?= DetailView::widget([
            'model' => $modelConnection,
//            'options' => [
//            'class' => 'table table-striped table-bordered detail-view detailviewconnection',
//            ],
        	'formatter' => [
        		'class' => 'yii\i18n\Formatter',
        		'nullDisplay' => ''
        	],
            'attributes' => [
                'start_date',
                'conf_date',
                //'activ_date',
                'pay_date',
                'close_date',
            ],
        ]);
        ?>
	</div>
   
	<?= DetailView::widget([
		'model' => $modelConnection,
//		'options' => [
//		'class' => 'table table-striped table-bordered detail-view detailviewconnection',
//		],
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
        ?>
        
        
        
        <?php if(isset($arInstallations)):?>
        <?= Collapse::widget([
            'items' => $arInstallations,
            
        ]);
        ?>
        <?php endif; ?>
    </div>
    
</div>

