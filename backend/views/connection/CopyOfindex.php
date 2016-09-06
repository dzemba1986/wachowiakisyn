<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use dosamigos\datepicker\DatePicker;
use backend\models\Type;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Połączenia';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="connection-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
	<?php Modal::begin([
		'id' => 'modal_create_installation',	
		'header' => '<center><h3>Dodaj instalację</h3></center>',
		'size' => 'modal-sm',	
	]);
	
	echo "<div id='modal_content'></div>";
	
	Modal::end(); ?>
    
	<?php Pjax::begin(['id'=>'connection-grid']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
				'header'=>'Lp.',
				'class'=>'yii\grid\SerialColumn',
			],
        	[
        		'attribute'=>'start_date',
        		'value'=>'start_date',
        		'format'=>'raw',
        		'filter'=>	DatePicker::widget([
    				'model' => $searchModel,
    				'attribute' => 'start_date',
        			'language'=>'pl',	
    				//'template' => '{addon}{input}',
        			'clientOptions' => [
            			'autoclose' => true,
            			'format' => 'yyyy-mm-dd'
        			]
				]),
        		'options' => ['style'=>'width:7%;'],
        	],	
        	[
        		'attribute'=>'fullAddress',
        		'value'=>'modelAddress.fullAddress',
        		'options' => ['style'=>'width:15%;'],
        	],
        	[
        		'attribute'=>'type',
        		'value'=>'modelType.name',
        	 	'filter'=> Html::activeDropDownList($searchModel, 'type', ArrayHelper::map(Type::find()->all(), 'id', 'name'), ['prompt'=>'', 'class'=>'form-control']),
        		'options' => ['style'=>'width:5%;'],
        	],
        	[
        		'header' => 'Kabel',
        		'format' => 'raw',
        		'value' => function($data){
        			if (sizeof($data->modelInstallationByType)  == 1)
        				return ArrayHelper::getValue($data->modelInstallationByType, '0.attributes.wire_date');
        			elseif (sizeof($data->modelInstallationByType)  > 1){
        				$i = 0;
	        			foreach ($data->modelInstallationByType as $objInstallation){
	        				$arInstallation[$i] = ArrayHelper::getValue($data->modelInstallationByType, $i.'.attributes.wire_date');
	        				$i++;
	        			}
	        			return Html::dropDownList('installation', 'id', $arInstallation, ['class'=>'form-control']);
        			}	
        			else 
        				//return Html::a('dodaj', '', ['id'=>'link_create_installation',]);
        				return Html::button('dodaj', ['value'=>Url::to('index.php?r=installation/wire-create&connectionId='.$data->id), 'class'=>'button_create_installation']);
        		},
        		'options' => ['style'=>'width:7%;'],
        	],
        	[
        	    'attribute' => 'socketDate', // it can be 'attribute' => 'tableField' to.
        		'header' => 'Gniazdo',
        		'format' => 'raw',
        		'value' => function($data) {
        			if(sizeof($data->modelInstallationByType) > 0){
        				$i=0;
        				$noSocket = 0;
        				foreach ($data->modelInstallationByType as $installation){
        					$arInstallation[$i] = $installation->attributes;
        						
        					if($arInstallation[$i]['socket_date'] == null)
        						$noSocket++;
        						$i++;
        				}
        				if(sizeof($data->modelInstallationByType) == $noSocket)
        					return 'brak';
        				elseif(sizeof($data->modelInstallationByType) == 1)
        					return $arInstallation[0]['socket_date'];
        				else
        					return Html::dropDownList('ins', 'id', ArrayHelper::map($arInstallation, 'id', 'socket_date'), ['class'=>'form-control']);
        			}
        			else
        				return 'brak';
        		},
        		'options' => ['style'=>'width:7%;'],
        	],
            [
        		'attribute'=>'conf_date',
        		'value'=>'conf_date',
        		'format'=>'raw',
        		'filter'=>	DatePicker::widget([
    				'model' => $searchModel,
    				'attribute' => 'conf_date',
        			'language'=>'pl',	
    				//'template' => '{addon}{input}',
        			'clientOptions' => [
            			'autoclose' => true,
            			'format' => 'yyyy-mm-dd'
        			]
				]),
            	'options' => ['style'=>'width:7%;'],
        	],	
            'info:ntext',
            'info_boa:ntext',
            // 'modyfication',

            [
            	'class' => 'yii\grid\ActionColumn',
            	'template' => '{view} {update} {delete}',
            	/* 'buttons' => [
            		[
            			'delete' => function ($url, $model) {
            				return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                        		'title' => Yii::t('app', 'Info'),
            				]);
            			},
            		],
        		], */
        		/* 'urlCreator' => function ($action, $model) {
        			if ($action === 'delete') {
        				$url ='task/create?conId='.$model->id;
        				return $url;
        			}
        		} */
        		
        		
        	],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>

<?php $script = <<< JS

$(function(){
	$('.button_create_installation').click(function(){
		$('#modal_create_installation').modal('show')
			.find('#modal_content')
			.load($(this).attr('value'));
	});
});

JS;
$this->registerJs($script);
?>