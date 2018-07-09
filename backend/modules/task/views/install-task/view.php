<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\models\Modyfication $model
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Modyfications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modyfication-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'start',
            'end',
            'url:url',
            'all_day',
            'color',
            'address',
            'description',
            'add_user',
            'installer',
            'cost',
        ],
    ]) ?>

</div>
