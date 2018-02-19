<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-store',
    'header' => '<center><h4>Dodaj do magazynu</h4></center>',
    'size' => 'modal-sm',
    'options' => [
        'tabindex' => false,
    ],
]);

echo "<div id='modal-content-store'></div>";

Modal::end(); 
?>
