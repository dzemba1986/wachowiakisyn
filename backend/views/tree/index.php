<?php

use yii\helpers\Url;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'SEU';
$this->params['breadcrumbs'][] = $this->title;
//var_dump(Yii::$app->request->BaseUrl);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jstree/dist/jstree.min.js');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/jstree/dist/themes/default/style.min.css');

?>

<!-------------------------------------------- otwórz port select okno modal ------------------------------------------>

	<?php Modal::begin([
		'id' => 'modal-port-select',	
		'header' => '<center><h4>Wyberz port</h4></center>',
		'size' => 'modal-sm',	
	]);
	
	echo "<div id='modal-content-port-select'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<!-------------------------------------------- otwórz replace from store okno modal ------------------------------------------>

	<?php Modal::begin([
		'id' => 'modal-replace-store',	
		'header' => '<center><h4>Zamień</h4></center>',
		'size' => 'modal-lg',
		'options' => [
			'tabindex' => false // important for Select2 to work properly
		],
	]);
	
	echo "<div id='modal-content-replace-store'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->  


<div id="device_tree" class="col-sm-4 sidebar"></div>
<div id="device_desc" class="col-sm-8 tabbable tabs-left"></div>                      

<script>
    
$(function() {

	function getId(id) {
      	return id.substr(0, id.indexOf("."));
    }       
        
        //tworzymy drzewo urządzeń
        $("#device_tree").jstree({
        	'core' : {
        		'themes' : {
            		//'name' : 'default-dark',	
        		    'variant' : 'large'
        		},
        		'data' : {
        			'url' : '<?= Url::toRoute('tree/get-children') ?>',
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
        		'check_callback': true
        	},
	   		 // Configuring the search plugin
	   		 'search' : {
	   			 // As this has been a common question - async search
	   			 // Same as above - the `ajax` config option is actually jQuery's AJAX object
	   			 'ajax' : {
	   				 'url' : '<?= Url::toRoute('tree/search') ?>',
	   				 // You get the search string as a parameter
// 	   				 "data" : function (str) {
// 	   					 return { 
// 	   						 "operation" : "search", 
// 	   						 "search_str" : str 
// 	   					 }; 
// 	   				 }
	   			 }
	   		 },
        	'contextmenu' : {
                "items": function (node) {

                	var tree = $("#device_tree").jstree(true);
                    
                    return {
                        "Update": {
                            "label": "Edytuj",
                            "action": function () {

                    			$("#device_desc").load("<?= Url::toRoute('device/tabs-update') ?>&id=" + getId(node.id));	
                            }
                        },
                        "Store": {
                            "label" : "Magazyn",
                            "submenu" : {
								"tostore" : {
									"label" : "Przenieś do magazynu",
									"action" : function (obj) { tree.delete_node(node); }
								},
								"replacestore" : {
									"label" : "Zamień z magazynu",
									"action" : function () {

										$('#modal-replace-store').modal('show').find('#modal-content-replace-store').load("<?= Url::toRoute('tree/replace-from-store') ?>&device=" + getId(node.id));	
		                            }
								}
                            }    
                            
                        },
                        "operation": {
                            "label": "Operacje",
                            "submenu" : {
                                "cut" : {
                                    "label" : "Wytnij",
                                    "action": function (obj) { tree.cut(node); }
                                },
                                "copy" : {
                                    "label" : "Kopiuj",
                                    "action": function (obj) { tree.copy(node); }
                                },
                                "paste" : {
                                    "label" : "Wklej",
                                    "action": function (obj) { tree.paste(node); }
                                }
                            }
                        }                     
                	}    
                }
            },

            "types" : {
                "valid_children" : [ "web" ],
                "types" : {
                    "web" : {
                        "icon" : { 
                            "image" : "/arco/Menu/images/web.png" 
                        },
                    },
                    "default" : {
                    	"icon" : { 
                            "image" : "/arco/Menu/images/web.png" 
                        },
                    }
                }
            },
            
        	'plugins' : ['contextmenu', 'search', 'types']    	
        });

        $("#searchTree").click(function() {
            $("#device_tree").jstree("search", $("#device_tree_q").val());
            return false;
        });
      
    
        //lewy przycisk dla węzła
        $("#device_tree").on(
            "select_node.jstree",
            function(node, event) {
                // The clicked node is "event.node"
                var node = event.node;

                $("#device_desc").load('<?= Url::toRoute('device/tabs-view') ?>&id=' + getId(node.id));                    
            }
        );

        $("#device_tree").on(
        	"paste.jstree",
            function(e, data) {

				var device = getId(data.node[0].original.id);
				var port = data.node[0].original.port;
				var newParentDevice = getId(data.parent);
        		var mode = data.mode;

				if (mode == 'move_node') {
				
	        		$('#modal-port-select').modal('show').find('#modal-content-port-select').load('<?= Url::toRoute(['tree/port-select', 'mode' => 'move']) ?>');
	        		$('#modal-port-select').data({
	            		device : device,
	            		port : port,
	            		newParentDevice : newParentDevice,
	            	});
				} else if (mode == 'copy_node'){
// 					console.log(mode);
					$('#modal-port-select').modal('show').find('#modal-content-port-select').load('<?= Url::toRoute(['tree/port-select', 'mode' => 'copy']) ?>');
	        		$('#modal-port-select').data({
	            		device : device,
	            		newParentDevice : newParentDevice,
	            	});
				}
      		}
       	); 

        $("#device_tree").on(
        	"delete_node.jstree",
            function(e, data) {

        		var tree = $("#device_tree").jstree(true);
        		var node = data.node; 
        		var port = data.node.original.port;

				if (tree.is_parent(node)){
                	alert("Nie można usunąć urządzenia do którego jest coś podłączone");
					tree.refresh();
				}
				else {

					$.get(
	                	'<?= Url::toRoute('tree/to-store') ?>',
	                	{'id' : getId(node.id), 'port' : port}
	                ).done(function(result){
	                			
//	                	console.log(result);
	                	if(result == 1){
	                		tree.refresh();
	                	}
	                	else{
	                		$('#message').html(result);
	                	}
	                }).fail(function(){
	                	console.log('server error');
	               	});
				}                  
            }
       	);

//         $("#device_tree").on(
//             "ready.jstree",
//             function(e, data) {

//             	$('.jstree-anchor').each(function(){
//             		$(this).children(":first").before($(this).text().substr(0, $(this).text().indexOf(":")+1));
//             		//$(this).text($(this).text().substring($(this).text().indexOf(":"), 3));	
//             	});                 
//         	}
// 		);
});
    
</script>


