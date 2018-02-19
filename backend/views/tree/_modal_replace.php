<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-replace-store',
    'header' => '<center><h4>Zamień</h4></center>',
    'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
]);

echo "<div id='modal-content-replace-store'></div>";

Modal::end(); 
?>