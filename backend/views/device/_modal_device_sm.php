<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-device-sm',
    'header' => '<center><h4>Tytuł</h4></center>',
    'size' => 'modal-sm',
]);

echo "<div id='modal-content-device-sm'></div>";

Modal::end(); 
?>