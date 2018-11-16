<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-sm',
    'header' => '<center><h4 id="modal-sm-title">Modal</h4></center>',
    'size' => 'modal-sm',
    'options' => [
        'tabindex' => false // by wiget `Select2` działał prawidłowo
    ],
]);

echo "<div id='modal-sm-content'></div>";

Modal::end(); 
?>