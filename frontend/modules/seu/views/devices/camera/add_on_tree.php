<?php


use common\models\seu\network\Vlan;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var frontend\modules\seu\models\forms\AddDeviceOnTreeForm $model
 */
?>

	<div class="row no-gutter">

    	<?= Html::label('Sieć', null, ['class' => 'col-md-5']) ?>
    
    </div>
    
    <div class="row no-gutter">
		
		<div class="col-sm-3">
    	
            <?= Html::activeDropDownList($model, 'vlan', ArrayHelper::map(Vlan::find()->select('id')->orderBy('id')->all(), 'id', 'id'), [
                'class' => 'form-control',
                'template' => '{input}\n{hint}\n{error}',
                'prompt' => 'Vlan',
                'onchange' => new JsExpression("
            		$.get('" . Url::to(['subnet/list']) . "&vlanId=' + $(this).val(), function(data){
                		$('select[name=\"AddDeviceOnTreeForm[subnet]\"]').html(data).trigger('change');
                    });
                ")  	
    		]) ?>
    		
    		<?= Html::error($model, 'vlan'); ?>
    		
		</div>
		
		<div class="col-sm-5">
		
    		<?= Html::activeDropDownList($model, 'subnet', [], [
                'class' => 'form-control', 
    			'prompt' => 'Podsieć',
                'onchange' => new JsExpression("
            		$.get('" . Url::to(['ip/select-list']) . "&subnet=' + $(this).val() + '&mode=free', function(data){
        				$('select[name=\"AddDeviceOnTreeForm[ip]\"]').html(data);
        			});
                ")  	
    		]) ?>
    		
    		<?= Html::tag('p', '', ['class' => 'help-block help-block-error']); ?>
    		
    	</div>
    		
		<div class="col-sm-4">
		
    		<?= Html::activeDropDownList($model, 'ip', [], [
                'class' => 'form-control', 
    			'prompt' => 'Ip',
    		]) ?>
    		
    		<?= Html::tag('p', '', ['class' => 'help-block help-block-error']); ?>
    		
		</div>    
    
	</div>
    
    <div class="row no-gutter">
    
    	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']); ?>
    
    </div>