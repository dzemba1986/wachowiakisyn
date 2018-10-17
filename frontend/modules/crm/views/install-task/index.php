<?php


/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var InstallTaskSearch $searchModel
 */

$this->title = 'Montaże';
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