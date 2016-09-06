<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Connection */

$this->title = 'Create Connection';
$this->params['breadcrumbs'][] = ['label' => 'Connections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="connection-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
