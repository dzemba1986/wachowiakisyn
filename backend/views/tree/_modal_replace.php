<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-replace',
    'header' => '<center><h4>Zamień</h4></center>',
    'size' => 'modal-md',
    'options' => [
        'tabindex' => false,
    ],
]);

echo "<div id='modal-content-replace'></div>";

Modal::end(); 
?>