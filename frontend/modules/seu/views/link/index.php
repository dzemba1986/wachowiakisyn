<?php

use components\jstree\JsTreeAsset;
use kartik\helpers\Html;
use components\clipboardjs\ClipboardJsAsset;

/** 
 * @var $this yii\web\View
 */ 

JsTreeAsset::register($this);
ClipboardJsAsset::register($this);

echo $this->renderFile('@app/views/modal/modal.php');
echo $this->renderFile('@app/views/modal/modal_sm.php');

$this->title = 'SEU';
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'col-sm-4']);
echo Html::input('text', 'search', null, ['class' => 'search form-control', 'placeholder' => "Szukaj..."]);
echo Html::checkbox('like', false, ['id' => 'like']);
echo Html::label('Zawiera', 'like');
echo Html::tag('div', '', ['id' => 'device_tree', 'class' => 'sidebar']);
echo Html::endTag('div');
echo Html::tag('div', '', ['id' => "device_desc", 'style' => 'position: fixed; right:-2%;', 'class' => 'col-sm-8 tabbable tabs-left']);

$id = json_encode(Yii::$app->request->get('id'));
$js = <<<JS
$(function() {
    $('.search').keyup(function(e) {
		if (e.which == 13)  $('#device_tree').jstree('search', $(this).val()); // gdy wcisnieto [enter]
    });

    $("#device_tree").jstree({
    	'core' : {
    		'themes' : {
    		    'variant' : 'large'
    		},
    		'data' : {
    			'url' : 'get-children',
    		    'data' : function (node) {
    		    	return { 
        		    	'id' : function (obj, callback) {
            		    	if (node.id == '#') 
            		    		return 1;
            		    	else 
            		    		return node.original.seuId;
            		    } 
    		    	};
    		    }
    		},
    		'check_callback' : function (op, node, parent, position, more) {
                if (op === "copy_node") return node.original.copy
                if (op === "delete_node") {
                    if (!node.original.delete || this.is_parent(node)) return false;
                }
            }
    	},
        'search' : {
            'ajax' : {
                'url' : 'search',
   				'dataType' : 'json',
                'data' : {
                    'like' : document.getElementById("like").checked
                }
            },
            'search_callback' : function (str, node) {
                var test = false;
                if (document.getElementById("like").checked == false) {
                    if ((node.original.seuId == str || node.original.name == str.toUpperCase() || node.original.proper_name == str.toLowerCase()) || node.original.network.mac == str.replace(/\W/g, '')) test = true;
                } else {
                    if ((node.original.seuId == str || node.original.name.includes(str.toUpperCase()) || node.original.proper_name.includes(str.toLowerCase())) || node.original.network.mac == str.replace(/\W/g, '')) test = true;
                }
                 
                if (!test) {
                    var ips = node.original.network.ips;
    	         	ips.forEach(function(element){
       		         	if(element.ip == str) {
       	   		         	test = true;
       		         	} 	
       		        });
                }

                return test;
	        }
        },
        'contextmenu' : {
            'items': function (node) {
                var tree = $("#device_tree").jstree(true);
                return {
                    'Update': {
                        'label' : 'Edycja',
                        'action' : function () {
                            $('#device_desc').load('/seu/devices/' + node.original.controller + '/tabs-update?id=' + node.original.seuId);
                        }
                    },
                    'Operation': {
                        'label' : 'Operacje',
                        'submenu' : {
                            'cut' : {
                                'label' : 'Wytnij',
                                'action' : function (obj) { tree.cut(node); }
                            },
                            'copy' : {
                                'label' : 'Kopiuj',
                                'action' : function (obj) { tree.copy(node); }
                            },
                            'paste' : {
                                'label' : 'Wklej',
                                'action': function (obj) { tree.paste(node); }
                            }
                        }
                    },
                    'Store' : {
                        'label' : 'Magazyn',
                        'submenu' : {
                            'tostore' : {
    							'label' : 'Przenieś do magazynu',
    							'action' : function (obj) { tree.delete_node(node); },
//                                 '_disabled' : function () {return node.original.delete == false;}
    						},
    						'replacestore' : {
    							'label' : 'Zamień z magazynu',
    							'action' : function () {
                                    $('#modal').modal('show').find('#modal-content').load('/seu/devices/' + node.original.controller + '/replace?id=' + node.original.seuId);
                                },
//                                 '_disabled' : function () {return (node.original.controller == 'host-ethernet' || node.original.controller == 'host-rfog');}
    						}
                        },
                        '_disabled' : function () {return node.original.delete == false;}
                    },
                    'Add': {
                        'label' : 'Dodaj',
                        'submenu' : {
                            'device' : {
                                'label' : 'Urządzenie z magazynu',
                                'action' : function () {
                                    if (node.original.type != 2 && node.original.type != 8) return false;
    							    $('#modal').modal('show').find('#modal-content').load('/seu/devices/device/add-on-tree?parentId=' + node.original.seuId);	
                                }
                            },
                            'virtual' : {
                                'label' : 'Virtualka',
                                'action' : function () {
                                    if (node.original.type != 2) return false;
    							    $('#modal-sm').modal('show').find('#modal-sm-content').load('/seu/devices/virtual/add-on-tree?parentId=' + node.original.seuId);	
                                }
                            },
                            'host' : {
                                'label' : 'Nieaktywny host',
                                'action' : function () {
                                    if (node.original.type != 2) return false;
    							    $('#modal-sm').modal('show').find('#modal-sm-content').load('/seu/devices/host-ethernet/add-inactive-on-tree?id=' + node.original.seuId);	
                                }
                            }
                        },
                        '_disabled' : function () { return node.original.children == false; }
                    }                             
            	}    
            }
        },

    	'plugins' : ['contextmenu', 'search']    	
    });

	$('#device_tree')
        //przejscie z LP do SEU
        .on('ready.jstree', function(e, data) {
            var id = $id;
        	if (id) { data.instance.search(id); }  
        })
        .on('search.jstree', function (e, data) {
            if (data.nodes.length == 1) data.instance.select_node(data.res); 
        })
        .on('select_node.jstree', function(e, data) {
            var node = data.node;
            $('#device_desc').load('/seu/devices/' + node.original.controller + '/tabs-view?id=' + node.original.seuId);
        })
        .on('move_node.jstree', function(e, data) {
            var deviceId = getId(data.node.id);
            var port = getPort(data.node.id);
            var newParentId = getId(data.parent);
            $('#modal-sm').modal('show').find('#modal-sm-content').load('/seu/link/move?deviceId=' + deviceId + '&port=' + port + '&newParentId=' + newParentId);
        })
        .on('copy_node.jstree', function(e, data) {
            var deviceId = getId(data.original.id);
            var parentId = getId(data.parent);
            $('#modal-sm').modal('show').find('#modal-sm-content').load('/seu/link/copy?deviceId=' + deviceId + '&parentId=' + parentId);
        })
        .on('delete_node.jstree', function(e, data) {
            var node = data.node;
            $('#modal-sm').modal('show').find('#modal-sm-content').load('/seu/devices/' + node.original.controller + '/delete-from-tree?id=' + node.original.seuId + '&port=' +  node.original.port);
        });      
});
JS;
$this->registerJs($js);
?>