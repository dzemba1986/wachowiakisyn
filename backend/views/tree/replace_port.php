<?php
use backend\models\Device;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var backend\models\Tree[] $links
 * @var backend\models\Device $sourceDevice
 * @var integer $destinationDeviceId
 * @var $onetoone
 */

echo "<div>";
echo Html::checkbox('onetoone', false, ['label' => '"1 do 1"', 'disabled' => !$onetoone]);
echo '</div>';

foreach ($links as $link) {
    
    $string = Device::findOne($link['device'])->address->toString(true);
    $dropDown = Html::dropDownList('map[' . $link['port'] . ']',  null, [], ['class' => 'port form-control', 'port' => $link['port']]);
    echo '<div class="col-md-4">';
    echo "{$sourceDevice->model->port[$link['port']]} - {$string} $dropDown";
    echo '</div>';
}

$urlPortList = Url::to(['tree/list-port']);
$js = <<<JS
$(function(){
    $.get('{$urlPortList}&deviceId=' + {$destinationDeviceId} + '&install=true&mode=all', function(data){
		$('.port').html(data);
	});

    $('.port').on('change', function () {
        var val = $(this).val();
        var i = $(this).index();
        $('.port:not(:eq('+i+'))').each(function () {
            if ($(this).val() === val) {
                alert($(this).index()+' has the same value');
            }
        });
    });

    $('input[name="onetoone"]').change(function() {
        if(this.checked) {
            $('.port').each(function(){
                $(this).val($(this).attr('port'));
            });
        } else {
            $('.port').each(function(){
                $(this).val(null);
            });
        }
    });
});
JS;
$this->registerJs($js);
?>