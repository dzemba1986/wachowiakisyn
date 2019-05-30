<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\task\models\InstallTask $task
 * $var backend\models\Address $
 * $var backend\models\Connection $connection
 * @var yii\widgets\ActiveForm $form
 */

?>

<?php $form = ActiveForm::begin(['id' => 'chose-task']); ?>

	<?= Html::radioList('tasks', null, ['serwis', 'montaż']); ?>
	
    <?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
    $( '#modal-title' ).html('Wybierz typ zadania');
JS;

$this->registerJs($js);
?>