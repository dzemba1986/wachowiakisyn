<?php

use common\models\crm\FullCalendarAsset;
use common\models\soa\Connection;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 */

echo $this->renderFile('@app/views/modal/modal.php');
FullCalendarAsset::register($this);

echo Html::tag('div', ['id' => 'calendar']);

$js = <<<JS
$(function() {
    $('#calendar').fullCalendar({
        header : {
			left : 'prev, next, today',
			center : 'title',
			right : 'agendaDay, agendaWeek'
		},
        lang : 'pl',
		timezone : 'local',
		defaultView : 'agendaWeek',
        minTime : '09:00',
		maxTime : '16:00',
		slotDuration : '01:00:00',
        hiddenDays : [0], //ukryj niedzielę
		allDaySlot : false,
		contentHeight : 530,
		height : 530,
        eventSources: [
            {
                url: '/crm/install-task/get',
                color: '#336600',
                textColor: 'black',
                editable: false,
            },
        ],
    });
});
JS;

$this->registerJs($js);
?>

<script>

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

	$('.modal-header h4').html('Kalendarz');
});


</script>
