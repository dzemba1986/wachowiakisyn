<?php

use common\models\crm\FullCalendarAsset;
use yii\helpers\Html;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\modules\task\models\InstallTaskSearch $searchModel
 */

echo $this->renderFile('@app/views/modal/modal.php');

FullCalendarAsset::register($this);
echo $this->renderFile('@app/views/modal/modal.php');
$this->params['breadcrumbs'][] = 'Zadania';

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-lg-2']);
        echo Html::label('Kalendarz', 'calendar_view');
        echo Html::beginTag('div', ['class' => 'input-group']);
            echo Select2::widget([
                'name' => 'calendar',
                'data' => [
                    'Serwis' => 'Serwis',
                    'Szczurek' => 'Szczurek',
                ],
                'options' => [
                    'id' => 'calendar_filter',
                    'class' => 'filter',
                    'multiple' => true
                ],
            ]);
        echo Html::endTag('div');
    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'col-lg-2']);
        echo Html::label('Typ', 'calendar_view');
        echo Html::beginTag('div', ['class' => 'input-group']);
            echo Select2::widget([
                'name' => 'type',
                'data' => [
                    'Montaż' => 'Montaż',
                    'Serwis' => 'Serwis',
                    'Urządzenie' => 'Urządzenie',
                    'Własne' => 'Własne',
                    'Blokada' => 'Blokada',
                ],
                'options' => [
                    'id' => 'type_filter',
                    'class' => 'filter',
                    'multiple' => true
                ],
            ]);
        echo Html::endTag('div');
    echo Html::endTag('div');
echo Html::endTag('div');

echo Html::tag('div', '', ['id' => 'contextMenu', 'class' => 'dropdown clearfix']);
echo Html::tag('div', '', ['id' => 'calendar', 'style' => 'padding-top:30px']);

$js = <<<JS
$(function() {
    $( '#calendar' ).fullCalendar({
        header : {
			left : 'prev, next, today, printButton',
			center : 'title',
			right : 'agendaDay, agendaWeek'
		},
        defaultView: 'agendaWeek',
        height: 830,
        minTime: '06:00:00',
        maxTime: '22:00:00',
        timezone : 'local',
        lang : 'pl',
        eventRender: function(event, element, view) {
            var event_type;
            
            if (event.type == 1) event_type = 'Serwis';
            else if (event.type == 2) event_type = 'Urządzenie';
            else if (event.type == 3) event_type = 'Montaż';
            else if (event.type == 4) event_type = 'Własne';
            else event_type = 'Blokada/rezerwacja';

            element.popover({
                title: event.title,
                content: '<div class="popoverInfoCalendar">' +
                         '<p><strong>Typ:</strong> ' + event_type + '</p>' +
                         '<p><strong>Kategoria:</strong> ' + event.category + '</p>' + 
                         '<div class="popoverDescCalendar"><strong>Opis:</strong> '+ event.description +'</div>' +
                         '</div>',
                delay: { 
                   show: "800", 
                   hide: "50"
                },
                trigger: 'hover',
                placement: 'top',
                html: true,
                container: 'body'
            });

            var show_type = true, show_calendar = true;

           var types = $('#type_filter').val();
           var calendars = $('#calendar_filter').val();

           if (types && types.length > 0) {
               if (types[0] == "all") {
                   show_type = true;
               } else {
                   show_type = types.indexOf(event.type) >= 0;
               }
           }

           if (calendars && calendars.length > 0) {
               if (calendars[0] == "all") {
                   show_calendar = true;
               } else {
                   show_calendar = calendars.indexOf(event.calendar) >= 0;
               }
           }

           return show_type && show_calendar;
    
        },
        customButtons: {
          printButton: {
            icon: 'print',
            click: function() {
              window.print();
            }
          }
        },
        eventSources: [
            {
                url: '/crm/install-task/get',
                color: '#336600',
                textColor: 'black',
                editable: false,
            },
            {
                url: '/crm/blockage/get',
                color: 'black',
                textColor: 'withe',
                editable: false,
            },
        ],
        dayClick : function(date, element, view) {
            
            var today = moment();
            var startDate;
            var endDate;
            var timestamp = date.format('X');
            if(view.name == "month") {
                startDate.set({ hours: today.hours(), minute: today.minutes() });
                startDate = moment(startDate).format('ddd DD MMM YYYY HH:mm');
                endDate = moment(endDate).subtract('days', 1);
                endDate.set({ hours: today.hours() + 1, minute: today.minutes() });
                endDate = moment(endDate).format('ddd DD MMM YYYY HH:mm');           
            } else {
                startDate = moment(startDate).format('ddd DD MMM YYYY HH:mm');
                endDate = moment(endDate).format('ddd DD MMM YYYY HH:mm');
            }

            var contextMenu = $("#contextMenu");
            var HTMLContent = '<ul class="dropdown-menu dropNewEvent" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">' +
                '<li><a tabindex="-1" href="/crm/install-task/create?timestamp=' + timestamp + '" onclick="$(\'#modal\').modal(\'show\').find(\'#modal-content\').load($(this).attr(\'href\')); return false;">Dodaj montaż</a></li>' +
                '<li><a tabindex="-1" href="/crm/self-task/create?timestamp=' + timestamp + '" onclick="$(\'#modal\').modal(\'show\').find(\'#modal-content\').load($(this).attr(\'href\')); return false;">Dodaj własne</a></li>' +
                '<li><a tabindex="-1" href="/crm/blockage/create?timestamp=' + timestamp + '" onclick="$(\'#modal\').modal(\'show\').find(\'#modal-content\').load($(this).attr(\'href\')); return false;">Dodaj blokadę</a></li>' +
                '<li class="divider"></li>' +
                '<li><a tabindex="-1" href="#">Zamknij</a></li>' +
            '</ul>';
          
            $(".fc-body").unbind('click');
            $(".fc-body").on('click', 'td', function (e) {
              
            document.getElementById('contextMenu').innerHTML = (HTMLContent);

            contextMenu.addClass("contextOpened");
                contextMenu.css({
                    display: "block",
                    left: e.pageX,
                    top: e.pageY
                });
                return false;
            });

            contextMenu.on("click", "a", function(e) {
                e.preventDefault();
                contextMenu.removeClass("contextOpened");
                contextMenu.hide();
            });
         
            $('body').on('click', function() {
                contextMenu.hide();
                contextMenu.removeClass("contextOpened");
            });
        },

	    eventClick : function(event, element, view) {
            var event_controller;
	    	var today = moment();
            var startDate;
            var endDate;

            if (event.type == 1) event_controller = 'service-task';
            else if (event.type == 2) event_controller = 'device-task';
            else if (event.type == 3) event_controller = 'install-task';
            else if (event.type == 4) event_controller = 'self-task';
            else event_controller = 'blockage';

            if(view.name == "month") {
                startDate.set({ hours: today.hours(), minute: today.minutes() });
                startDate = moment(startDate).format('ddd DD MMM YYYY HH:mm');
                endDate = moment(endDate).subtract('days', 1);
                endDate.set({ hours: today.hours() + 1, minute: today.minutes() });
                endDate = moment(endDate).format('ddd DD MMM YYYY HH:mm');           
            } else {
                startDate = moment(startDate).format('ddd DD MMM YYYY HH:mm');
                endDate = moment(endDate).format('ddd DD MMM YYYY HH:mm');
            }
         
            var contextMenu = $("#contextMenu");
             
            var HTMLContent = '<ul class="dropdown-menu dropNewEvent" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">' +
                '<li> <a tabindex="-1" href="/crm/' + event_controller + '/update?id=' + event.id + '" onclick="$(\'#modal\').modal(\'show\').find(\'#modal-content\').load($(this).attr(\'href\')); return false;">Edycja</a></li>' +
                '<li> <a tabindex="-1" href="/crm/' + event_controller + '/close?id=' + event.id + '" onclick="$(\'#modal\').modal(\'show\').find(\'#modal-content\').load($(this).attr(\'href\')); return false;">Zakończ</a></li>' +
                '<li class="divider"></li>' +
                '<li><a tabindex="-1" href="#">Zamknij</a></li>' +
            '</ul>';
              
            $(".fc-body").unbind('click');
            $(".fc-body").on('click', 'td', function (e) {
                document.getElementById('contextMenu').innerHTML = (HTMLContent);
    
                contextMenu.addClass("contextOpened");
                contextMenu.css({
                    display: "block",
                    left: e.pageX,
                    top: e.pageY
                });
                return false;
    
            });
    
            contextMenu.on("click", "a", function(e) {
                e.preventDefault();
                contextMenu.removeClass("contextOpened");
                contextMenu.hide();
            });
             
            $('body').on('click', function() {
                contextMenu.hide();
                contextMenu.removeClass("contextOpened");
            });
	    },
    });

    $('.filter').on('change', function() {
       $('#calendar').fullCalendar('rerenderEvents');
   });
});


JS;

$this->registerJs($js);
?>