<?php

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\ModyficationSearch $searchModel
 */

$this->title = 'Zadania';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-index">

<?php 
if ($mode == 'todo'){

	echo $this->render('grid_todo', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider
	]);
} elseif ($mode == 'close'){

	echo $this->render('grid_close', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider
	]);
}

?>

</div>

<script>
    
$(document).ready(function() {

    //reinicjalizacja kalendarza z datami po użyciu pjax'a
    $("#task-grid-pjax").on("pjax:complete", function() {
        
    	if (jQuery('#tasksearch-start_date').data('kvDatepicker')) { jQuery('#tasksearch-start_date').kvDatepicker('destroy'); }
    	  jQuery('#tasksearch-start_date-kvdate').kvDatepicker(kvDatepicker_00747738);
    });
});

</script>