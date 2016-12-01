<?php
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Połączenia';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Boa';
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

<!-------------------------------------------- gridview umowy --------------------------------------------------------->

<div class="connection-index">  
    
 <?php 
	echo $this->render('grid_boa', [
		'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
	]);
?>    

<!---------------------------------------------------------------------------------------------------------------------> 

</div>

<script>
    
$(document).ready(function() {
        
    $('body').on('click', 'a[title="View"]', function(event){
        
        //event.preventDefault();
        
		$('#modal-connection-view').modal('show')
			.find('#modal-content-connection-view')
			.load($(this).attr('href') + ' .connection-view');
    
        return false;
	});

    //reinicjalizacja kalendarza z datami po użyciu pjax'a
    $("#connection-grid-pjax").on("pjax:complete", function() {
        
        if ($('#connectionsearch-start_date').data('kvDatepicker')) { $('#connectionsearch-start_date').kvDatepicker('destroy'); }
        $('#connectionsearch-start_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('connectionsearch-start_date');
        if ($('#task-start').data('kvDatepicker')) { $('#task-start').kvDatepicker('destroy'); }
        $('#task-start-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('task-start');
        if ($('#connectionsearch-conf_date').data('kvDatepicker')) { $('#connectionsearch-conf_date').kvDatepicker('destroy'); }
        $('#connectionsearch-conf_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('connectionsearch-conf_date');
        if ($('#connectionsearch-pay_date').data('kvDatepicker')) { $('#connectionsearch-pay_date').kvDatepicker('destroy'); }
        $('#connectionsearch-pay_date-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('connectionsearch-pay_date');
        if ($('#start').data('kvDatepicker')) { $('#start').kvDatepicker('destroy'); }
        $('#start-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('start');
        if ($('#conf').data('kvDatepicker')) { $('#conf').kvDatepicker('destroy'); }
        $('#conf-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('conf');
        if ($('#activ').data('kvDatepicker')) { $('#activ').kvDatepicker('destroy'); }
        $('#activ-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('activ');
        if ($('#pay').data('kvDatepicker')) { $('#pay').kvDatepicker('destroy'); }
        $('#pay-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('pay');
        if ($('#move_phone').data('kvDatepicker')) { $('#move_phone').kvDatepicker('destroy'); }
        $('#move_phone-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('move_phone');
        if ($('#resignation').data('kvDatepicker')) { $('#resignation').kvDatepicker('destroy'); }
        $('#resignation-kvdate').kvDatepicker(kvDatepicker_d5532c14);

        initDPAddon('resignation');

        if (jQuery('#connectionsearch-task').data('kvDatepicker')) { jQuery('#connectionsearch-task').kvDatepicker('destroy'); }
        jQuery('#connectionsearch-task-kvdate').kvDatepicker(kvDatepicker_00747738);

        initDPAddon('connectionsearch-task');

        if (jQuery('#connectionsearch-street').data('select2')) { jQuery('#connectionsearch-street').select2('destroy'); }
        jQuery.when(jQuery('#connectionsearch-street').select2(select2_817d1b80)).done(initS2Loading('connectionsearch-street','s2options_d6851687'));
    });
});

</script>