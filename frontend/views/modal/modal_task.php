<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-task',
    'header' => '<center><h4 id="modal-task-title">Modal</h4></center>',
    'options' => [
        'tabindex' => false // by wiget `Select2` działał prawidłowo
    ],
]);

echo "<div id='modal-task-content'></div>";

Modal::end(); 
?>