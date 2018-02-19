<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-connection',
    'header' => '<center><h4>Wybierz port</h4></center>',
    'size' => 'modal-sm',
]);

echo "<div id='modal-content-connection'></div>";

Modal::end(); 
?>