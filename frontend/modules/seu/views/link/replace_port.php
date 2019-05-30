<?php
use common\models\seu\devices\Device;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var backend\models\Tree[] $links
 * @var backend\models\Device $sDevice
 * @var integer $dId
 * @var boolean $onetoone
 */

echo Html::checkbox('onetoone', false, ['label' => '"1 do 1"', 'disabled' => !$onetoone]);
echo '<div class="help-block"></div>';

echo "<div class='row'>";
foreach ($links as $link) {
    
    $string = Device::findOne($link['device'])->name;
    $dropDown = Html::dropDownList("map[{$link['port']}]",  null, [], ['class' => 'port form-control', 'port' => $link['port'], 'required']);
    echo "<div class='col-md-4'>";
    echo "{$sDevice->model->port[$link['port']]} - {$string} $dropDown";
    echo '<div class="help-block"></div>';
    echo '</div>';
}
echo '</div>';
echo '<div class="form-group">';
echo Html::submitButton('Zamień', ['class' => 'change btn btn-primary']);
echo '</div>';

$urlPortList = Url::to(['link/list-port', 'deviceId' => $dId, 'install' => true, 'mode' => 'all']);
$js = <<<JS
$(function(){
    $.get('{$urlPortList}', function(data){
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