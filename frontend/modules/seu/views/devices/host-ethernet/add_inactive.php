<?php
use common\models\seu\network\Vlan;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var backend\models\forms\AddHostForm $model
 * @var \yii\widgets\ActiveForm $form
 * @var backend\models\Host $host
 */

$form = ActiveForm::begin([
	'id' => $model->formName(),
    'validationUrl' => Url::to(['add-host-validation'])
]);
?>

	<?= Html::label("Umowa zostanie przypisana do przełącznika " . Html::a($host->configParent->firstIp, "ssh://{$host->configParent->firstIp}:22222") . "/{$host->configParent->portName}") ?>
	
	<div class="row">

		<?= $form->field($model, 'mac', [
    	    'enableAjaxValidation' => true,
    	    'options' => ['class' => 'col-sm-5', 'style' => 'padding-right: 3px;'],
	   ]) ?>


        <?= $form->field($model, 'vlanId', [
            'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px;'],
        ])->dropDownList(ArrayHelper::map(Vlan::find()->select('id')->orderBy('id')->all(), 'id', 'id'), [
        	'prompt' => 'Vlan',
        	'onchange' => new JsExpression("
        		$.get('" . Url::to(['subnet/list']) . "&vlanId=' + $(this).val(), function(data){
            		$('select[name=\"AddHostEthernetForm[subnetId]\"]').html(data).trigger('change');
        		});	
        	")  	
        ]) ?>

	</div>
	
	<div class="row">        
    	
    	<?= $form->field($model, 'subnetId', [
            'options' => ['class' => 'col-sm-5', 'style' => 'padding-right: 3px;'],
        ])->dropDownList([], [
        	'prompt' => 'Podsieć',
        	'onchange' => new JsExpression("
        		$.get('" . Url::to(['ip/select-list']) . "&subnet=' + $(this).val() + '&mode=free', function(data){
    				$('select[name=\"AddHostEthernetForm[ip]\"]').html(data);
    			});
        	")  	
        ]) ?>
        
        <?= $form->field($model, 'ip', [
            'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px;'],
        ])->dropDownList([], [
        	'prompt' => 'Ip',  	
        ]) ?>
</div>

<?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>	

<?php
$js = <<<JS
$(function() {
    $('#{$model->formName()}').on('beforeSubmit', function(e){
		var form = $(this);
     	$.post(
      		form.attr('action'),
      		form.serialize()
     	).done(function(result){
 			if(result){
                $('#modal').modal('hide');
 			}
 			else{
 				alert(result);
 			}
 		}).fail(function(){
 			console.log('server error');
 		});
		return false;				
	});
});
JS;
$this->registerJs($js);
?>