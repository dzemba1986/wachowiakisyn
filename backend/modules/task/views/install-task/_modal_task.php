<?php 

use yii\bootstrap\Modal;

Modal::begin([
	'id' => 'modal-task',
	'header' => '<center><h4>Zadanie</h4></center>',
	'size' => 'modal-sm',
	'options' => [
		'tabindex' => false // dla poprawnego działania widget'u `Select2`
	],
]);

echo "<div id='modal-task-content'></div>";

Modal::end(); 

?>