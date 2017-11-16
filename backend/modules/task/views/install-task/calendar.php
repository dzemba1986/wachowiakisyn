<?php

use backend\models\Connection;
use backend\models\Task;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $model backend\models\Connection
 */
require_once '_modal_task.php';

$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lib/qtip/jquery.qtip.min.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lib/moment.min.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/fullcalendar.min.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lang-all.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxcore.js');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxmenu.js');

$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lib/qtip/jquery.qtip.min.css');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/fullcalendar.min.css');


?> 
<div id="calendar"></div>

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
		defaultView : 'agendaWeek',
		defaultDate : '<?= \Yii::$app->session->has('connectionId') && is_object(Connection::findOne(\Yii::$app->session->get('connectionId'))->task) ? Connection::findOne(\Yii::$app->session->get('connectionId'))->task->start : date("Y-m-d H:i:s") ?>',   
		minTime : '09:00',
		maxTime : '16:00',
		slotDuration : '01:00:00',
		hiddenDays : [0], //ukryj niedzielę
		allDaySlot : false,
		contentHeight : 530,
		height : 530,
		eventSources : '<?= Url::to(['view-task-calendar']) ?>',
		eventRender: function(event, element) {
	        element.qtip({
	            content: event.description
	        });
	    }, 		
		dayClick : function(date, jsEvent, view) {
	        	
	        	$('#modal-task').modal('show')
                .find('#modal-task-content')
                .load('<?= Url::to(['create']) ?>&timestamp=' + date);
	    },
	    eventClick : function(date, jsEvent, view) {

	    	$('#modal-task').modal('show')
            .find('#modal-task-content')
            .load('<?= Url::to(['update']) ?>&id=' + date.id);

            return false;
	    },
	});

	$("body").bind("DOMNodeInserted", function() {
	    $(this).find('tr[data-time="12:00:00"]').css("background-color", "#f2f2f2");
	});
});


</script>
