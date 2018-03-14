<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var backend\models\Device $sourceDevice
 * @var backend\models\Device $destinationDevice
 */

//kamrey RH-164
if ($sourceDevice->model_id == 19 && $destinationDevice->model_id == 19) {
    echo Html::checkbox('replaceMac', false, ['id' => 'replace-mac', 'label' => "Podmień mac'i urządzeń"]);
}

echo Html::input('hidden', 'map[0]', 0);
echo '</br>';
echo '<div class="form-group">';
echo Html::submitButton('Zamień', ['class' => 'change btn btn-primary', 'data-clipboard-text' => '', 'disabled' => true]);

echo Html::button('Skrypt', ['class' => 'btn script', 'onclick' => new JsExpression("
    $.get('" . Url::to(['device/get-change-mac-script']) . "&deviceId=" . $sourceDevice->id . "&newMac=" . $destinationDevice->mac . "', function(data){
        $('.change').attr('disabled', false);
		$('.change').attr('data-clipboard-text', data);
	});
")]);
echo '</div>';

$urlView = Url::to(['device/tabs-view']);

$js = <<<JS
$(function(){
    var clipboard = new Clipboard('.change');

    if ($('#replace-mac').is('input[name="replaceMac"]')) {
        $('input[name="replaceMac"]').change(function() {
            if(this.checked) {
                $('.script').attr('disabled', true);
                $('.change').attr('disabled', false);
            } else {
                $('.script').attr('disabled', false);
                $('.change').attr('disabled', true);
            }
        });
    }
});
JS;
$this->registerJs($js);
?>
