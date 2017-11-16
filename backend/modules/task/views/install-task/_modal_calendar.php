<?php 

use yii\bootstrap\Modal;

Modal::begin([
	'id' => 'modal-calendar',
	'header' => '<center><h4>Kalendarz zadań</h4></center>',
	'size' => 'modal-lg',
		'options' => [
				'tabindex' => false // important for Select2 to work properly
		],
]);

echo "<div id='modal-content-calendar'></div>";

Modal::end(); 
?>