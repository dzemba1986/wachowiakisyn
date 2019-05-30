<?php

/**
 * @var \yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Adresacja';
$this->params['breadcrumbs'][] = 'SEU';
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@app/views/modal/modal_sm.php');

echo GridView::widget([
    'id' => 'vlan-grid',
    'options' => ['class' => 'col-xs-3'],
    'dataProvider' => $dataProvider,
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'vlan-grid-pjax'
        ]    
    ],
    'summary' => 'Widoczne {count} z {totalCount}',
    'resizableColumns' => false,
    'columns' => [
        'id',	
    	'desc',
    	[
    		'class' => 'yii\grid\ActionColumn',
    		'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', ['vlan/create'], [
                'onclick' => "
                    $( '#modal-sm' ).modal('show').find( '#modal-sm-content' ).load($(this).attr('href'));
	    
                    return false;
                "
    		]),
    		'buttons' => [
    			'view' => function ($model, $data) {
    				$url = Url::to(['network/subnet/index', 'vlan' => $data->id]);
    				return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
    					'title' => \Yii::t('yii', 'Widok podsieci'),
    					'data-pjax' => true,
    				    'onclick' => "
                            $( '#subnet-grid' ).load($(this).attr('href'));
    				    
                            return false;
                        "
    				]);
    			},
    			'update' => function ($url, $model, $data) {
    				return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
    					'title' => \Yii::t('yii', 'Edycja'),
    					'data-pjax' => '0',
    				    'onclick' => "
                            $( '#modal-sm' ).modal('show').find( '#modal-sm-content' ).load($(this).attr('href'));
    				    
                            return false;
                        "
    				]);
    			},
    			'delete' => function ($url, $model, $data) {
    				return Html::a('<span class="glyphicon glyphicon-trash"></span>', false, [
    					'title' => \Yii::t('yii', 'Usuń'),
    				    'onclick' => "
                            if (confirm('Czy na pewno usunąć rekord?')){
                    	    	$.ajax({
                    	        	url: $(this).attr('{$url}'),
                    	            type: 'post',
                    	            dataType: 'json',
                    	            error: function(xhr, status, error) {
                    	            	alert('There was an error with your request.' + xhr.responseText);
                    	            }
                    	        }).done(function(data) {
                    	        	$.pjax.reload({container: '#vlan-grid-pjax'});
                    	        });
                    		}

                            return false;
                        "
    				]);
    			},
    		]
    	],
    ]                
]); 
    
echo '<div id="subnet-grid" class="col-xs-5"></div>';
echo '<div id="ip-grid" class="col-xs-4"></div>';
?>