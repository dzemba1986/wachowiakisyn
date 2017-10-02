<?php 
use yii\bootstrap\Modal;

/**
 * Modal dialog for update
 */
Modal::begin([
	'id' => 'modal-update',
	'header' => '<center><h4>Edycja</h4></center>',
	'size' => 'modal-sm',
]);

echo "<div id='modal-content'></div>";

Modal::end(); 
?>