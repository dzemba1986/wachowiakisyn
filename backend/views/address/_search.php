<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AddressSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 't_woj') ?>

    <?= $form->field($model, 't_pow') ?>

    <?= $form->field($model, 't_gmi') ?>

    <?= $form->field($model, 't_rodz') ?>

    <?php // echo $form->field($model, 't_miasto') ?>

    <?php // echo $form->field($model, 't_ulica') ?>

    <?php // echo $form->field($model, 'prefix_ulica') ?>

    <?php // echo $form->field($model, 'ulica') ?>

    <?php // echo $form->field($model, 'dom') ?>

    <?php // echo $form->field($model, 'dom_szczegol') ?>

    <?php // echo $form->field($model, 'lokal') ?>

    <?php // echo $form->field($model, 'lokal_szczegol') ?>

    <?php // echo $form->field($model, 'nazwa_inna') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
