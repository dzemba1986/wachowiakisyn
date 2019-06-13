<?php

/**
 * @var yii\web\View $this
 * @var common\models\crm\Comment $comment
 * @var common\models\crm\Comment[] $comments
 */

use yii\helpers\Html;
// var_dump($comments); exit();
foreach ($comments as $comment) {
    echo Html::beginTag('div', ['class' => 'col']);
        echo Html::tag('p', $comment['desc']);
        echo Html::beginTag('p');
            echo Html::tag('span', $comment['create_at'] . ' ', ['style' => 'color:#2E8B57; font-size:10px; margin-right:20px;']);
            echo Html::tag('span', $comment['last_name'], ['style' => 'color:#c55; font-size:10px;']);
        echo Html::endTag('p');
        echo Html::tag('hr');
    echo Html::endTag('div');
}

$js = <<<JS
$(function() {
    $( '#modal-title' ).html('Komentarze');
});
JS;

$this->registerJs($js);
?>