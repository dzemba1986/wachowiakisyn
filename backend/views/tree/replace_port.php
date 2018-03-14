<?php
use backend\models\Device;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var backend\models\Tree[] $links
 * @var backend\models\Device $sourceDevice
 * @var integer $destinationDeviceId
 * @var boolean $onetoone
 */

echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo Html::checkbox('onetoone', false, ['label' => '"1 do 1"', 'disabled' => !$onetoone]);
echo '</div>';
echo '</div>';

echo "<div class='row'>";
foreach ($links as $link) {
    
    $string = Device::findOne($link['device'])->name;
    $dropDown = Html::dropDownList("map[{$link['port']}]",  null, [], ['class' => 'port form-control', 'port' => $link['port']]);
    echo "<div class='col-md-4'>";
    echo "{$sourceDevice->model->port[$link['port']]} - {$string} $dropDown";
    echo '</div>';
}
echo '</div>';
echo '</br>';
echo '<div class="form-group">';
echo Html::submitButton('Zamień', ['class' => 'change btn btn-primary']);
echo '</div>';

$urlPortList = Url::to(['tree/list-port']);
$js = <<<JS
$(function(){
    $.get('{$urlPortList}&deviceId=' + {$destinationDeviceId} + '&install=true&mode=all', function(data){
		$('.port').html(data);
	});

    $('.port').on('change', function () {
        $('.port').each(function () {
            var val = $(this).val();
            var i = $(this).index('.port');
            $('.port:not(:eq(' + i + '))').each(function () {
                if ($(this).val() == val) {
                    $('.port').eq($(this).index('.port')).css('border-color', '#a94442');
                    $('.port').eq(i).css('border-color', '#a94442');
                } else { 
                    $('.port').eq(i).css('border-color', '');
                }
            });
        });
    });

//     $('.port').on('change', function () {
//         var val = $(this).val();
//         var i = $(this).index('.port');
//         $('.port').eq(i).css('border-color', '');
//         $('.port:not(:eq(' + i + '))').each(function () {
//             if ($(this).val() == val) {
//                 //alert($(this).index('.port') + ' ma tą samą wartość');
//                 $('.port').eq($(this).index('.port')).css('border-color', '#a94442');
//                 $('.port').eq(i).css('border-color', '#a94442');
//             } else { 
//                 $('.port').eq($(this).index('.port')).css('border-color', '');
//             }
//         });
//     });

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