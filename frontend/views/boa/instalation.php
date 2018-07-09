<?php

use yii\widgets\DetailView;

echo  DetailView::widget([
    'model' => $model,
    'attributes' => [
        'wire_length',
        'wire_date',
        'socket_date',
        'wire_user',
        'socket_user',
        'invoice_date',
    ],
]) ?>