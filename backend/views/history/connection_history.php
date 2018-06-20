<?php


/**
 * @var yii\web\View $this
 * @var array $histories
 */
echo '<div>';
echo '<table id="w0" class="table table-striped table-bordered detail-view">';
echo '<tbody>';
echo "<tr><th>Kiedy</th><th>Kto</th><th>Opis</th></tr>";
foreach ($histories as $history) {
    echo "<tr><td>{$history['created_at']}</td><td>{$history['last_name']}</td><td>{$history['desc']}</td></tr>";
}
echo '</tbody>';
echo '</table>';
echo '</div>';

$this->registerJs(
"$(function(){
	$('.modal-header h4').html('Historia');
});"
);
?>