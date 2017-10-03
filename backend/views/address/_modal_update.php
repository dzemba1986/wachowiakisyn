<?php 
use yii\bootstrap\Modal;

/**
 * Modal dialog for update
 */
Modal::begin([
	'header' => '<center><h4>Edycja</h4></center>',
	'size' => 'modal-sm',
	'options' => [
		'id' => 'modal-update',
		'tabindex' => false // by wiget `Select2` działał prawidłowo
	],
]);

echo "<div id='modal-content'></div>";

Modal::end(); 
?>