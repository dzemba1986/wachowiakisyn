<?php 

use common\models\seu\devices\DeviceType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'add-store-form'
]); ?>

<div class="row">

	<div class="col-sm-1 form-group">
		<?= Html::dropDownList('device_type', '', ArrayHelper::map(DeviceType::findByController()->all(), 'controller', 'name'), [
		    'prompt' => 'Wybierz...',
            'class' => 'form-control'
		]) ?>
	</div>
	
	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-success col-md-1', 'onclick' => "
        if( !$(\"select[name='device_type']\").val() )
			$.growl.error({ message: 'Nie wybrano urządzenia'});
		else {
		  $('#modal-sm').modal('show').find('#modal-sm-content').load('?r=seu/' +  $(\"select[name='device_type']\").val() + '/add-on-store');
    	}
    	
        return false;
    "]) ?>

</div>

<?php ActiveForm::end(); ?>