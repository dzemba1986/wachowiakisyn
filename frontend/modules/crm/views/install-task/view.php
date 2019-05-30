<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use common\models\crm\Task;

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
        'id',
        'address',
        'create_at',
        [
            'label' => Html::tag('span', 'Termin umówienia', ['style' => 'color:#2E8B57;']),
            'value' => function ($task) {
                return date('Y-m-d H:i', strtotime($task->start_at)) . ' - ' . date('H:i', strtotime($task->end_at));
            }
        ],
        [
            'label' => Html::tag('span', 'Termin wykonania', ['style' => 'color:#c55;']),
            'value' => function ($task) {
                if ($task->exec_from && $task->exec_to) return date('Y-m-d', strtotime($task->exec_from)) . ' - ' . date('Y-m-d', strtotime($task->exec_to));
            }
        ],
        [
            'label' => 'Autor',
            'value' => $task->createBy->last_name,
        ],
        'desc',
        'cost',
        [
            'label' => 'Płatnik',
            'value' => function ($task) {
                return Task::PAY_BY[$task->pay_by];
            }
        ],
        'done_by',
        'close_at',
    ],
]);

$js = <<<JS
$(function() {
    $('#modal-title').html('Podgląd montażu');
});
JS;

$this->registerJs($js);

