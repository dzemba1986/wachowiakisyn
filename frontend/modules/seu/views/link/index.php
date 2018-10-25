<?php

use yii\helpers\Url;

/** 
 * @var $this yii\web\View
 */ 

$this->registerJsFile('@web/js/jstree/dist/jstree.min.js');
$this->registerCssFile('@web/js/jstree/dist/themes/default/style.min.css');
$this->registerJsFile('@web/js/clipboard.min.js');
$this->registerJsFile('@web/js/jquery-url-min.js');

echo $this->renderFile('@app/views/modal/modal.php');
echo $this->renderFile('@app/views/modal/modal_sm.php');

$this->title = 'SEU';
$this->params['breadcrumbs'][] = $this->title;

echo '<div class="col-sm-4">';
echo '<input class="search form-control"></input>';
echo '<div id="device_tree" class="sidebar"></div>';
echo '</div>';
echo '<div id="device_desc" style="position: fixed; right:-2%;" class="col-sm-8 tabbable tabs-left"></div>';

$urlGetChildren = Url::to(['get-children']);
$urlSearch = Url::to(['search']);
$urlMove = Url::to(['move']);
$urlCopy = Url::to(['copy']);

$js = <<<JS
$(function() {

    function getId(id) {
      	return id.substr(0, id.indexOf('.'));
    }

    function getPort(id) {
      	return id.substr(id.indexOf('.') + 1);
    }

    $('.search').keyup(function(e) {
		// gdy wcisnieto [enter]
		if (e.which == 13) {
	        var searchString = $(this).val();
	        $('#device_tree').jstree('search', searchString);
		}
    });

    $("#device_tree").jstree({
    	'core' : {
    		'themes' : {
    		    'variant' : 'large'
    		},
    		'data' : {
    			'url' : '{$urlGetChildren}',
    		    'data' : function (node) {
    		    	return { 
        		    	'id' : function (obj, callback) {
            		    	if (node.id == '#') 
            		    		return 1;
            		    	else 
            		    		return getId(node.id);
            		    } 
    		    	};
    		    }
    		},
    		'check_callback' : function (op, node, parent, position, more) {

                if (op === "copy_node") {   
                    //tylko routery i przełączniki
                    if (node.original.type == 1 || node.original.type == 2) return true;
                    else return false;    
                }

                if (op === "delete_node") {   
                    if (this.is_parent(node)) return false;
                    if (node.original.type == 5) return false;
                }
            }
    	},
        'search' : {
            'ajax' : {
                'url' : '{$urlSearch}',
   				'dataType' : 'json',
            },
            'search_callback' : function (str, node) {
                var test = false;
	            
                if(node.id == str || node.original.name.includes(str.toUpperCase()) || node.original.network.mac == str.toLowerCase()) {
                    test = true; 
                }
	         	
                var ips = node.original.network.ips;
	         	ips.forEach(function(element){
   		         	if(element.ip == str){
   	   		         	test = true;
   		         	} 	
   		        });

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
                            $('#device_desc').load('?r=seu/' + node.original.controller + '/tabs-update&id=' + getId(node.id));
                        }
                    },
                    'Store' : {
                        'label' : 'Magazyn',
                        'submenu' : {
                            'tostore' : {
    							'label' : 'Przenieś do magazynu',
    							'action' : function (obj) { tree.delete_node(node); }
    						},
    						'replacestore' : {
    							'label' : 'Zamień z magazynu',
    							'action' : function () {
                                    if (node.original.type == 5) return false;
    							    $('#modal').modal('show').find('#modal-content').load('?r=seu/' + node.original.controller + '/replace&id=' + getId(node.id));	
                                }
    						}
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
                    'Add': {
                        'label' : 'Dodaj',
                        'submenu' : {
                            'device' : {
                                'label' : 'Urządzenie z magazynu',
                                'action' : function () {
                                    if (node.original.type != 2) return false;
    							    $('#modal').modal('show').find('#modal-content').load('?r=seu/device/add-on-tree&parentId=' + getId(node.id));	
                                }
                            },
                            'virtual' : {
                                'label' : 'Virtualka',
                                'action' : function () {
                                    if (node.original.type != 2) return false;
    							    $('#modal-sm').modal('show').find('#modal-sm-content').load('?r=seu/virtual/add-on-tree&id=' + getId(node.id));	
                                }
                            },
                            'host' : {
                                'label' : 'Nieaktywny host',
                                'action' : function () {
                                    if (node.original.type != 2) return false;
    							    $('#modal-sm').modal('show').find('#modal-sm-content').load('?r=seu/host-ethernet/add-inactive-on-tree&id=' + getId(node.id));	
                                }
                            }
                        }
                    }                             
            	}    
            }
        },
        'types' : {
            'valid_children' : [ 'web' ],
            'types' : {
                'web' : {
                    'icon' : { 
                        'image' : '/arco/Menu/images/web.png' 
                    },
                },
                'default' : {
                	'icon' : { 
                        'image' : '/arco/Menu/images/web.png' 
                    },
                }
            }
        },
    	'plugins' : ['contextmenu', 'search', 'types']    	
    });

	$('#device_tree')
        //przejscie z LP do SEU
        .on('ready.jstree', function(e, data) {
        	data.instance.search($.url("?id")); 
        })

        .on('search.jstree', function (e, data) {
    	   data.instance.select_node(data.res); 
        })

        .on('select_node.jstree', function(e, data) {
            var node = data.node;

            $('#device_desc').load('?r=seu/' + node.original.controller + '/tabs-view&id=' + getId(node.id));                    
        })

        .on('move_node.jstree', function(e, data) {
            
            var deviceId = getId(data.node.id);
            var port = getPort(data.node.id);
            var newParentId = getId(data.parent);
            $('#modal-sm').modal('show').find('#modal-sm-content').load('{$urlMove}&deviceId=' + deviceId + '&port=' + port + '&newParentId=' + newParentId);
        })

        .on('copy_node.jstree', function(e, data) {
            
            var deviceId = getId(data.original.id);
            var parentId = getId(data.parent);
            $('#modal-sm').modal('show').find('#modal-sm-content').load('{$urlCopy}&deviceId=' + deviceId + '&parentId=' + parentId);
        })

        .on('delete_node.jstree', function(e, data) {
            var node = data.node;
            var deviceId = getId(data.node.id);
            var port = getPort(data.node.id);
            $('#modal-sm').modal('show').find('#modal-sm-content').load('?r=seu/' + node.original.controller + '/delete-from-tree&id=' + deviceId + '&port=' + port);
        });      
});
JS;
$this->registerJs($js);
?>