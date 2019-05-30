<style>
.modal-lg { 
    width: 80%;
}
</style>

<?php 
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'modal-calendar',
    'header' => '<center><h4 id="modal-calendar-title">Kalendarz</h4></center>',
    'size' => 'modal-lg',
    'options' => [
        'tabindex' => false, // by wiget `Select2` działał prawidłowo
    ],
]);

echo "<div id='modal-calendar-content'></div>";

Modal::end(); 
?>