<?php 

use yii\bootstrap\Modal;

Modal::begin([
	'id' => 'modal-comment',
	'header' => '<center><h4>Komentarz</h4></center>',
	'size' => 'modal-mm',
	'options' => [
		'tabindex' => false // dla poprawnego działania widget'u `Select2`
	],
]);

echo "<div id='modal-comment-content'></div>";

Modal::end(); 

?>