<div class="connection-view">
    
 <?php 
if ($modelConnection->type == 1){
	
	echo $this->render('_view_net', [
		'modelConnection' => $modelConnection,
	]);
} elseif ($modelConnection->type == 2){
	
	echo $this->render('_view_phone', [
			'modelConnection' => $modelConnection,
	]);
}
?>    

</div>