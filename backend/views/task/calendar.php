<?php

use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use backend\models\Task;
use yii\helpers\Url;
use backend\models\Connection;

$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lib/qtip/jquery.qtip.min.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lib/moment.min.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/fullcalendar.min.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lang-all.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxcore.js');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxmenu.js');

$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lib/qtip/jquery.qtip.min.css');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/fullcalendar.min.css');

/* @var $this yii\web\View */
/* @var $model backend\models\Connection */
?> 

<!-------------------------------------------- otwórz create/update task okno modal ----------------------------------->

<?php Modal::begin([
	'id' => 'modal-create-task',
	'header' => '<center><h4>Zadanie</h4></center>',
	'size' => 'modal-sm',
]);

	echo "<div id='modal-create-task-content'></div>";

Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->  
<?php Pjax::begin(['id' => 'calendar-pjax']); ?>
<div id="calendar"></div>
<?php Pjax::end(); ?>

<style>
/*    .fc-slats tr{
        height: 10px; 
    } */
</style>


<script type="text/javascript">

$(function() {

	$("#calendar").fullCalendar({
		header : {
			left : 'prev, next, today',
			center : 'title',
			right : 'agendaDay, agendaWeek'
		},
		lang : 'pl',
		timezone : 'local',
		defaultView : 'agendaDay',
		defaultDate : '<?= !is_null($conId) && is_object(Connection::findOne($conId)->modelTask) ? Connection::findOne($conId)->modelTask->start_date : date("Y-m-d H:i:s") ?>',   
		minTime : '08:00',
		maxTime : '18:00',
		slotDuration : '00:30:00',
		hiddenDays : [0], //ukryj niedzielę
		contentHeight : 500,
		height : 500,
		eventSources : '<?= Url::to(['task-calendar']) ?>',

		eventRender: function(event, element) {
	        element.qtip({
	            content: event.description
	        });
	    }, 		
		dayClick : function(date, jsEvent, view) {

			var conId = '<?= json_encode($conId) ?>';
			
	        if ( conId == 'null' ) { //jeżeli zadanie tworzymy poza LP
	        	
	        	$('#modal-create-task').modal('show')
                .find('#modal-create-task-content')
                .load('<?= Url::to(['task/create']) ?>&timestamp=' + date);
		    } 
		    else { //jeżeli zadanie tworzymy z LP

		    	$('#modal-create-task').modal('show')
                .find('#modal-create-task-content')
                .load('<?= Url::to(['task/create']) ?>&timestamp=' + date + '&conId=<?= $conId ?>');
			}
	    },
	    eventClick : function(date, jsEvent, view) {

	    	$('#modal-create-task').modal('show')
            .find('#modal-create-task-content')
            .load('<?= Url::to(['task/update']) ?>&id=' + date.id);

            return false;
	    },
// 	    eventMouseover : function(event, jsEvent, view){

// 			console.log(event.description);
// 		}
	});
});


</script>
