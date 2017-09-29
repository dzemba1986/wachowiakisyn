<?php 
use yii\bootstrap\Modal;

/**
 * Modal dialog for update Address model
 */
Modal::begin([
	'id' => 'modal-update-address',
	'header' => '<center><h4>Edycja adresu</h4></center>',
	'size' => 'modal-sm',
]);

echo "<div id='modal-content-calendar'></div>";

Modal::end(); 
?>