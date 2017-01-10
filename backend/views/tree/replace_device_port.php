<?php 

use backend\models\Device;
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jquery-ui.min.js', ['position' => \yii\web\View::POS_BEGIN]); 

?>

<style>

.dropzones {
    height: 2em;
    margin: 1em auto;
    background: #ddd;
}

.dragzones {
    background: #a00;
    color: #fff;
    height: 1em;
    width: 100%;
    padding: 0.5em 0;
    text-align: center;
	box-sizing: unset;
	position: absolute;
}

.taken {
        background: #aaa;
}

</style>


	<?php
	$modelDeviceSource = Device::findOne($deviceSource);
	
	//var_dump($modelsTree);
	
	echo '<div id="source" class="dropzones col-sm-4">';
	foreach ($modelsTree as $modelTree){
	
		echo '<div class="dragzones" id="' . ($modelTree['port']) . '">' . $modelDeviceSource->modelModel->port[$modelTree['port']] . ' - ' . Device::findOne($modelTree['device'])->modelAddress->fullDeviceAddress . '</div>';
		//var_dump($modelTree['port']);
	}
	
	echo '</div>';
	
	?>

<div id="destination">

	<?php
	
	$modelDeviceDestination = Device::findOne($deviceDestination);
	
	//id elementów - mapowanie na numery portów
	$counter = 0;
	foreach ($modelDeviceDestination->modelModel->port as $port){
		echo '<div class="dropzones col-sm-4" " id="' . $counter .'"><center>' . $port . '</center></div>';
		$counter++;
	}
	?>
</div>



<script>

$(function(){
	$ (init);
	function init() {
	    $(".dragzones").draggable({
	        start: function(event, ui){},
	        cursor: 'move',
	        revert: 'invalid',
	        opacity: .5,
	    });
	    $(".dropzones").droppable({
	        drop: function(event, ui){
	        	//Get the position before changing the DOM
	            var p1 = ui.draggable.parent().offset();
	            //Move the element to the new parent
	            $(this).append(ui.draggable);
	            //Get the postion after changing the DOM
	            var p2 = ui.draggable.parent().offset();
	            //Set the position relative to the change
	            ui.draggable
	            	.css({
	              		top: parseInt(ui.draggable.css('top')) + (p1.top - p2.top),
	              		left: parseInt(ui.draggable.css('left')) + (p1.left - p2.left)
	            	})
	            	.position({of: $(this), my: 'left top', at: 'left top'});

	            //$(this).droppable('destroy');
	        },
	        tolerance: "touch",
	        out: function(event, ui){
				$(this).removeClass('taken');
	        }
	    });
	}
});
</script> 