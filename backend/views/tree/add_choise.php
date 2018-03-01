<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 */

$form = ActiveForm::begin([
    'id' => 'join-host',
]);?>

<div class="form-group">
    	
	<?= Html::label('Wybierz hosta lub dodaj nowego:') ?>
    				
	<?php
	echo '<div>';
	foreach ($hosts as $host) {
	    if ($host->status) {
	        $color = 'green';
            $status = 1;
	    } else {
	        $color = 'red';
	        $status = 0;
        }
	    echo "<label><input name='host' value={$host->id} status={$status} type='radio'><font color={$color}> {$host->mixName}</font></label>";
	}
	echo "<label><input name='host' value='new' type='radio'> Nowy</label>";
	echo '</div>';
	?>
	
	<div id="desc"></div>	
</div>

<?= Html::button('Dalej', ['class' => 'next btn btn-primary', 'disabled' => true]) ?>

<?php ActiveForm::end() ?>

<?php
$url = Url::to(['tree/add-host', 'connectionId' => $connection->id]);

$js = <<<JS
$(function() {
    $('.modal-header h4').html('{$connection->address->toString()}');

    $('input[name="host"]').change(function() {
        $('.next').attr('disabled', false);
    });

    $('.next').click(function() {
        if ($('input[name="host"]:checked').val() == 'new')
            $('#modal-content-connection-add-tree').load('{$url}&hostId=new');
        else if ($('input[name="host"]:checked').attr('status') == 1)
            $('#modal-content-connection-add-tree').load('{$url}&hostId=' + $('input[name="host"]:checked').val());
        else if ($('input[name="host"]:checked').attr('status') == 0)
            $('#modal-content-connection-add-tree').load('{$url}&hostId=' + $('input[name="host"]:checked').val());
    });
});
JS;

$this->registerJs($js);
?>