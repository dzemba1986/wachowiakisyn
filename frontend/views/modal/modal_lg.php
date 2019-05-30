<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-lg',
    'header' => '<center><h4 id="modal-lg-title">Modal LG</h4></center>',
    'size' => 'modal-lg',
    'options' => [
        'tabindex' => false // by wiget `Select2` działał prawidłowo
    ],
]);

echo "<div id='modal-lg-content'></div>";

Modal::end(); 
?>