<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Address */

$this->title = $model->id;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-view">

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
//             't_woj',
//             't_pow',
//             't_gmi',
//             't_rodz',
//             't_miasto',
//             't_ulica',
            'ulica_prefix',
            'ulica',
            'dom',
            'dom_szczegol',
            'lokal',
            'lokal_szczegol',
        ],
    ]) ?>

</div>
