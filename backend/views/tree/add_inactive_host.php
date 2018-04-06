<?php
use backend\models\Vlan;
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
    'validationUrl' => Url::to(['host/add-host-validation'])
]);
?>

<div class="row">
	<?= Html::label("Umowa zostanie przypisana do hosta na porcie {$host->parentPortName} przełącznika o ip " . Html::a($host->parentIp, "ssh://{$host->parentIp}:22222"), null, ['class' => 'col-sm-8']) ?>

	<?= $form->field($model, 'mac', [
	    'enableAjaxValidation' => true,
	    'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px;'],
	]) ?>
</div>
	
<div class="row">
        <?= $form->field($model, 'vlanId', [
            'options' => ['class' => 'col-sm-3', 'style' => 'padding-right: 3px;'],
        ])->dropDownList(ArrayHelper::map(Vlan::find()->select('id')->orderBy('id')->all(), 'id', 'id'), [
        	'prompt' => 'Vlan',
        	'onchange' => new JsExpression("
        		$.get('" . Url::toRoute('subnet/list') . "&vlanId=' + $(this).val(), function(data){
            		$('select[name=\"AddHostForm[subnetId]\"]').html(data).trigger('change');
        		});	
        	")  	
        ]) ?>
        
        <?= $form->field($model, 'subnetId', [
            'options' => ['class' => 'col-sm-5', 'style' => 'padding-left: 0px; padding-right: 3px;'],
        ])->dropDownList([], [
        	'prompt' => 'Podsieć',
        	'onchange' => new JsExpression("
        		$.get('" . Url::toRoute('ip/select-list') . "&subnet=' + $(this).val() + '&mode=free', function(data){
    				$('select[name=\"AddHostForm[ip]\"]').html(data);
    			});
        	")  	
        ]) ?>
        
        <?= $form->field($model, 'ip', [
            'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px;'],
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