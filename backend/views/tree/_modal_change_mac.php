<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-change-mac',
    'header' => '<center><h4>Zmiana MAC</h4></center>',
    'size' => 'modal-sm',
    'options' => [
        'tabindex' => false, // important for Select2 to work properly
    ],
]);

echo "<div id='modal-content-change-mac'></div>";

Modal::end(); 
?>
