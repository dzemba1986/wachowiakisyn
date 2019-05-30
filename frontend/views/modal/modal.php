<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal',
    'header' => '<center><h4 id="modal-title">Modal</h4></center>',
    'options' => [
        'tabindex' => false // by wiget `Select2` działał prawidłowo
    ],
]);

echo "<div id='modal-content'></div>";

Modal::end(); 
?>