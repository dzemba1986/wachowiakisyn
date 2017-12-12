<?php
use backend\models\Connection;

/**
 * @var yii\web\View $this
 * @var backend\models\Connection $model 
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
 
$this->registerJs(
"$(function(){
	$('.modal-header h4').html('{$model->modelAddress->toString()}');
});"
);
?>   

