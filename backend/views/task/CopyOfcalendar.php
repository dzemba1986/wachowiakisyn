<?php

use yii\widgets\Pjax;
use yii2fullcalendar\yii2fullcalendar;
use yii\bootstrap\Modal;
use backend\models\Task;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/lib/moment.min.js');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/fullcalendar.min.js');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/fullcalendar/fullcalendar.min.css');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxcore.js');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxmenu.js');

/* @var $this yii\web\View */
/* @var $model backend\models\Connection */

Modal::begin([
	'id' => 'modal_task',
	'header' => '<center><h4>Dodaj / Edytuj zadanie</h4></center>',
	'size' => 'modal-sm',
]);

	echo "<div id='modal_content'></div>";

Modal::end(); 

?>

<div id="calendar"></div>


<style>
/*    .fc-slats tr{
        height: 10px; 
    } */
</style>


<div id="task_menu"></div>

<div class="calendar-view">
	<?php Pjax::begin(['id'=>'task-calendar']); ?>
	<?=	yii2fullcalendar::widget([
			'clientOptions' => [
                'header' => [
                    'right' => 'agendaDay, agendaWeek'
                ],
				'timezone' => 'local',
                //'startParam' => 'start_date'
				'defaultView' => 'agendaDay',
				//'hiddenDays' => [0], //ukryj niedzielę
				'minTime' => '09:00',
				'maxTime' => '18:00',
				//'scrollTime' => '18:00',
				'slotDuration' => '00:30:00',
				'contentHeight' => 500,
				'height' => 500,
                //'editable' => true,
                'defaultDate' => $modelConnection != null   ?   $modelConnection->task != null ? Task::findOne($modelConnection->task)->start_date : date("Y-m-d H:i:s")    :   date("Y-m-d H:i:s"),
                		
				'dayClick' => new \yii\web\JsExpression("function(date, jsEvent, view) {
                    
					console.log(" . json_encode(isset($modelConnection)). ");	
						
					if (" . json_encode(isset($modelConnection)). "){	
                        if (" . json_encode(!isset($modelConnection->task)) . ") {
                            $('#modal_task').modal('show')
                            .find('#modal_content')
                            .load('" . Url::to(['task/create']) . "&timestamp=' + date + '&connectionId=" . $modelConnection->id . "');
                        }
                        else {
                            $('#modal_task').modal('show')
                            .find('#modal_content')
                            .load('" . Url::to(['task/create']) . "&timestamp=' + date);
                        }
					} else {
						$('#modal_task').modal('show')
						.find('#modal_content')
						.load('" . Url::to(['task/create']) . "&timestamp=' + date);
					}					
				}"),
					             
				'eventClick' => new \yii\web\JsExpression("function(date, jsEvent, view) {
                    
                    $('#modal_task').modal('show')
                    .find('#modal_content')
                    .load('" . Url::to(['task/update']) . "&id=' + date.id);

                    return false;
				}"),
			],
            
			'header'=>[
				'left' => 'prev next today',
				'center'=> 'title',
				'right'=> 'month, agendaWeek, agendaDay'
			],
			'options' => [
	        	'lang' => 'pl',
                
					
					
				
	        	//... more options to be defined here!
	      	],
			'ajaxEvents' => yii\helpers\Url::to(['task-calendar']),
            //'gotoDate' => new \yii\web\JsExpression("$.fullCalendar.moment('2014-05-01')"),
	]);
	?>
	<?php Pjax::end(); ?>
</div>

<script type="text/javascript">

$(function() { 

	$("#calendar").fullCalendar({

	});
});
</script>
