<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal_add_tree',
    'header' => '<center><h4>Dodaj do drzewa</h4></center>',
    'size' => 'modal-mm',
    'options' => [
        'tabindex' => false,
    ],
]);

echo "<div id='modal_content_add_tree'></div>";

Modal::end();
?>