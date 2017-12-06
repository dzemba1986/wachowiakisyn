<?php
use kartik\grid\GridView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Połączenia';
$this->params['breadcrumbs'][] = $this->title;
?>
    
<!-------------------------------------------- widok połączenia okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-connection-view',	
		'header' => '<center><h4>Widok umowy</h4></center>',
		'size' => 'modal-mm',	
	]);
	
	echo "<div id='modal-content-connection-view'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<!-------------------------------------------- edycja połączenia okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-connection-update',	
		'header' => '<center><h4>Edycja umowy</h4></center>',
		'size' => 'modal-mm',
		'options' => [
				'tabindex' => false // important for Select2 to work properly
		],
	]);
	
	echo "<div id='modal-content-connection-update'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<!-------------------------------------------- edycja połączenia okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-connection-add-tree',	
		'header' => '<center><h4>Edycja umowy</h4></center>',
		'size' => 'modal-mm',
		'options' => [
				'tabindex' => false // important for Select2 to work properly
		],
	]);
	
	echo "<div id='modal-content-connection-add-tree'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<!-------------------------------------------- gridview umowy --------------------------------------------------------->

<div class="connection-index">  
    
<?php 
if ($mode == 'nopay' || $mode == 'install' || $mode == 'conf' || $mode == 'off' || $mode == 'pay' || $mode == 'all' || $mode == 'noboa' || $mode == 'boa'){
	
	echo $this->render("grid_{$mode}", [
		'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
	]);
}
?>    

<!---------------------------------------------------------------------------------------------------------------------> 

</div>

<script>
    
$(document).ready(function() {
        
    $('body').on('click', 'a[title="View"]', function(event){
        
        //event.preventDefault();
        
		$('#modal-connection-view').modal('show')
			.find('#modal-content-connection-view')
			.load($(this).attr('href'));
    
        return false;
	});

	$('body').on('click', 'a[title="Update"]', function(event){
        
        //event.preventDefault();
        
		$('#modal-connection-update').modal('show')
			.find('#modal-content-connection-update')
			.load($(this).attr('href'));
    
        return false;
	});

	$('body').on('click', 'a[title="Zamontuj"]', function(event){
        
		$('#modal-connection-add-tree').modal('show')
			.find('#modal-content-connection-add-tree')
			.load($(this).attr('href'));

    	return false;
	});
});

</script>