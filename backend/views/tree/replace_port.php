<?php
use backend\models\Device;
use yii\helpers\Html;
use yii\helpers\Url;

//TODO opcja powinna pojawiać się gdy model source = model destination
echo '<div>' . Html::checkbox('onetoone', false, ['label' => '"1 do 1"']) . '</div>';

foreach ($links as $link) {
    
    $string = Device::findOne($link['device'])->address->toString(true);
    $dropDown = Html::dropDownList('map[' . $link['port'] . ']',  null, [], ['class' => 'port form-control']);
    echo '<div class="col-md-3">';
    echo "{$deviceSource->model->port[$link['port']]} - {$string} $dropDown";
    echo '</div>';
}

$urlPortList = Url::to(['tree/list-port']);
$js = <<<JS
$(function(){
    $.get('{$urlPortList}&deviceId=' + $('#device-select').val() + '&install=true&mode=all', function(data){
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
});
JS;
$this->registerJs($js);
?>