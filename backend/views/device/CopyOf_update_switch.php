<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Address;
use yii\helpers\Url;
use backend\models\Device;
use backend\models\Vlan;
use backend\models\Subnet;

$form = ActiveForm::begin([
	'id' => 'update-device-form',
	//'enableClientValidation'=>true,
])?>

    <?= Html::label('Lokalizacja') ?>
    
    <div class="row">
    
    <?= $form->field($modelAddress, 'ulica', [
			'options' => ['class' => 'col-md-5', 'style' => 'padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->widget(Select2::className(), [
     		'data' => ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica'),
       		'options' => ['placeholder' => 'Ulica'],
       		'pluginOptions' => [
            	'allowClear' => true
            ],
        ])
    ?>
    
    <?= $form->field($modelAddress, 'dom' , [
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->textInput(['placeholder' => $modelAddress->getAttributeLabel('dom')]) 
    ?>
    
    <?= $form->field($modelAddress, 'dom_szczegol' , [
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->textInput(['placeholder' => $modelAddress->getAttributeLabel('dom_szczegol')]) 
    ?>
    
    <?= $form->field($modelAddress, 'pietro' , [
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList(Address::getFloor(), ['prompt' => $modelAddress->getAttributeLabel('pietro')]) 
    ?>
    
    </div>    
    
    
    <?= Html::label('Adresacja') ?>
    
    <?php foreach ($modelIps as $modelIp):?>
    
    <div class="row">

    	<?= $form->field($modelVlan, 'id', [
    		'options' => ['class' => 'col-md-4', 'style' => 'padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList(
    		ArrayHelper::map(Vlan::find()->all(), 
    			'id', 
    			function($modelVlan) {
			        return $modelVlan['id'].' - '.$modelVlan['desc'];
			    }
    		), 
    		[
    			'prompt' => $modelVlan->getAttributeLabel('id'),
    			'onchange' => '
                	$.get( "' . Url::toRoute('subnet/list-by-vlan') . '&vlan=" + $("select#vlan-id").val(), function(data){
						$("select#subnet-id").html(data);
					} );'
    		]
    	) 
    	?>
    	
    	<?= $form->field($modelSubnet, 'id', [
    		'options' => ['class' => 'col-md-4', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList(
    		ArrayHelper::map(Subnet::find()->all(), 
    			'id', 
    			function($modelSubnet) {
			        return $modelSubnet['ip'].' - '.$modelSubnet['desc'];
			    }
    		), 
    		[
    			'prompt' => $modelSubnet->getAttributeLabel('ip'),
    			'onchange' => '
                	$.get( "' . Url::toRoute('ip/free-ip-by-subnet') . '&subnet=" + $("select#subnet-id").val(), function(data){
						$("select#ip-ip").html(data);
					} );'
    		]
    	) 
    	?>
    	
    	<?= $form->field($modelIp, 'ip', [
    		'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList(
    		[], 
    		['prompt' => $modelIp->getAttributeLabel('ip')]
    	) 
    	?>
    </div>
    
    <?php endforeach; ?>
    
	<?= $form->field($modelDevice, 'mac') ?>				
    
    <?= $form->field($modelDevice, 'status')->checkbox() ?>
    
	<?= Html::submitButton($modelDevice->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
	
<?php ActiveForm::end() ?>

<script>


	
//	$.get( " <?= Url::toRoute('tree/free-port-list') ?>&id=" + $("select#tree-parent_device").val(), function(data){
// 		$("select#tree-parent_port").html(data);
// 	} );
	                    		
//	$.get( " <?= Url::toRoute('tree/free-port-list') ?>&id=" + <?= $modelDevice->id ?>, function(data){
// 		$("select#tree-port").html(data);
// 	} );

</script>

