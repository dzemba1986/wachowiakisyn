<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 * @var backend\models\Connection $connection
 * @var array $hosts Hosts Object Array
 */

$items = [];
foreach ($hosts as $host) {
    $items[$host->id] = $host->status ? '<font color=green>' . $host->mixName . '</font>' : '<font color=red>' . $host->mixName . '</font>';
}
$items['new'] = 'Nowy';

$form = ActiveForm::begin([
    'id' => 'join-host',
]);?>

	<?= Html::label('Wybierz hosta lub dodaj nowego:') ?>

	<?= Html::radioList('host', NULL, $items, ['encode' => FALSE]) ?>

	<?= Html::button('Dalej', ['class' => 'next btn btn-primary', 'disabled' => true]) ?>

<?php ActiveForm::end() ?>

<?php
$url = Url::to(['add-on-tree', 'connectionId' => $connection->id]);

$js = <<<JS
$(function() {
    $('.modal-header h4').html('{$connection->address->toString()}');

    $('input[name="host"]').change(function() {
        $('.next').attr('disabled', false);
    });

    $('.next').click(function() {
        if ($('input[name="host"]:checked').val() == 'new') $('#modal-content').load('{$url}&hostId=new');
        else $('#modal-content').load('{$url}&hostId=' + $('input[name="host"]:checked').val());
    });
});
JS;

$this->registerJs($js);
?>