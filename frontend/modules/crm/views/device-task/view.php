<?php

use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var common\models\crm\InstallTask $task
 */

echo DetailView::widget([
    'model' => $task,
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => ''
    ],
    'attributes' => [
        [
            'label' => 'Urządzenie',
            'value' => function ($task) {
                return $task->device->type->name;
            }
        ],
        'address',
        [
            'label' => 'Data dodania',
            'value' => $task->create_at,
        ],
        [
            'label' => 'Autor',
            'value' => $task->createBy->last_name,
        ],
        'desc',
        [
            'label' => 'Data zamkniecia',
            'value' => $task->close_at,
        ],
        'close_by',
        'fulfit',
    ],
]);

$js = <<<JS
$(function() {
    $('#modal-title').html('Podgląd zgłoszenia');
});
JS;

$this->registerJs($js);

