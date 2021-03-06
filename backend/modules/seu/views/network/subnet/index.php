<?php

/**
 * @var \yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var integer $vlan
 */

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

echo GridView::widget([
    'id' => 'subnet-grid',
    'dataProvider' => $dataProvider,
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'subnet-grid-pjax'
        ]    
    ],
    'summary' => 'Widoczne {count} z {totalCount}',
    'resizableColumns' => false,
    'columns' => [
    	'id',	
        [
            'label' => 'Podsieć',
            'attribute' => 'ip',
        ],
    	[
    	    'label' => 'Opis',
    	    'attribute' => 'desc',
	    ],
    	[
    		'class' => 'kartik\grid\BooleanColumn',
    		'attribute' => 'dhcp',
    	],
        [
            'label' => 'Wolne',
            'attribute' => 'freeips',
        ],
    	[
    		'class' => 'yii\grid\ActionColumn',
    	    'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', ['subnet/create', 'vlan' => $vlan], [
    	        'onclick' => "
                    $( '#modal-sm' ).modal('show').find( '#modal-sm-content' ).load($(this).attr('href'));
    	        
                    return false;
                "
    	    ]),
    		'template' => '{view} {update} {delete} {dhcp}',
    		'buttons' => [
    			'view' => function ($url, $data, $key) {
    				$url = Url::to(['ip/index', 'subnetId' => $data['id']]);
    				return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
    					'title' => \Yii::t('yii', 'Widok'),
    					'data-pjax' => true,
    				    'onclick' => "
                            $( '#ip-grid' ).load($(this).attr('href'));
    				    
                            return false;
                        "
    				]);
    			},
    			'update' => function ($url, $data, $key) {
                    $url = Url::to(['subnet/update', 'id' => $data['id']]);
    				return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
    					'title' => \Yii::t('yii', 'Edycja'),
    					'data-pjax' => '0',
    				    'onclick' => "
                            $( '#modal-sm' ).modal('show').find( '#modal-sm-content' ).load($(this).attr('href'));
    				    
                            return false;
                        "
    				]);
    			},
    			'delete' => function ($url, $data, $key) {
                    $url = Url::to(['subnet/delete', 'id' => $data['id']]);
    				return Html::a('<span class="glyphicon glyphicon-trash"></span>', false, [
    					'title' => \Yii::t('yii', 'Usuń'),
    				    'onclick' => "
                            if (confirm('Czy na pewno usunąć podsieć?')){
                    	    	$.ajax({
                    	        	url: '{$url}',
                    	            type: 'post',
                    	            dataType: 'json',
                    	            error: function(xhr, status, error) {
                    	            	alert('Błąd. ' + xhr.responseText);
                    	            }
                    	        }).done(function(data) {
                    	        	$.pjax.reload({container: '#subnet-grid-pjax'});
                    	        });
                    		}
                    		
                            return false;
                        "
    				]);
    			},
    			'dhcp' => function ($url, $data, $key) {
        			if($data['dhcp']){
        			    $url = Url::to(['dhcp-value/update', 'subnet' => $data['id']]);
        				return Html::a('D', $url, [
    						'title' => \Yii::t('yii', 'DHCP'),
    						'data-pjax' => '0',
        				]);
        			} else
        				return null;
    			},
    		]
    	],
    ]                
]); 
?>