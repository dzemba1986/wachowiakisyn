<div class="connection-view">
    
 <?php 
 if ($modelConnection->type == 1 || $modelConnection->type == 3){
	
	echo $this->renderAjax('_view_net', [
		'modelConnection' => $modelConnection,
	]);
} elseif ($modelConnection->type == 2){
	
	echo $this->renderAjax('_view_phone', [
			'modelConnection' => $modelConnection,
	]);
}
?>    

</div>