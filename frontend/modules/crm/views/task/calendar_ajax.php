<?php

use common\models\crm\FullCalendarAsset;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 */

echo $this->renderFile('@app/views/modal/modal_task.php');
FullCalendarAsset::register($this);

echo Html::tag('div', '', ['id' => 'calendar']);

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
        minTime : '06:00',
		maxTime : '22:00',
        hiddenDays : [0], //ukryj niedzielę
		allDaySlot : false,
		contentHeight : 730,
		height : 730,
        eventSources: [
            {
                url: '/crm/install-task/get',
                color: '#336600',
                textColor: 'black',
                editable: false,
            },
        ],
        dayClick : function(date, jsEvent, view) {
            var timestamp = date.format('X');
        	$('#modal-task').modal('show').find('#modal-task-content').load('/crm/install-task/create?timestamp=' + timestamp);
	    },
    });
});
JS;

$this->registerJs($js);
?>