<?php
use backend\models\Connection;

/**
 * @var Connection $model
 */
 
 if ($model->type == 1 || $model->type == 3){
	
	echo $this->renderAjax('_view_net', [
		'model' => $model,
	]);
} elseif ($model->type == 2){
	
	echo $this->renderAjax('_view_phone', [
			'model' => $model,
	]);
}
?>    

