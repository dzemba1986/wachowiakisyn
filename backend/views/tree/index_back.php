<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\grid\GridView;
use backend\models\Address;
use yii\widgets\ActiveForm;



/* @var $this yii\web\View */
/* @var $searchModel backend\models\AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'SEU';
$this->params['breadcrumbs'][] = $this->title;
//var_dump(Yii::$app->request->BaseUrl);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/tree.jquery.js');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/css/jqtree.css');

$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxcore.js');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxmenu.js');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/styles/jqx.shinyblack.css');

?>

<div>
<div id="device_tree" data-url="index.php?r=tree/get-children-node" style="width: 30%; display: -moz-grid-line"></div>
<div id="device_desc" style="width: 60%; display: -moz-grid-line; margin-left: 20px"></div>
<div id="device_menu"></div>                        

</div>

<script>
    
    $(function() { 
        
        //tworzymy menu kontekstowe
        $("#device_menu").jqxMenu({ width: 'auto', autoOpenPopup: false, mode: 'popup'});
        
        var data = [
            {
                label: "Centralny : ",
                "id": 1,
                "load_on_demand": true,
            },
        ];
        
        //tworzymy drzewo urządzeń
        $("#device_tree").tree({
            data: data,
            autoOpen: 0, //poziom 0 automatycznie rozwinięty
            onCreateLi: function(node, $li) {
                // Add 'icon' span before title
                $li.find('.jqtree-title').before('<span class="glyphicon glyphicon-hdd"></span> ');
            }
        });
    
        //lewy przycisk dla węzła
        $("#device_tree").bind(
            "tree.click",
            function(event) {
                // The clicked node is "event.node"
                var node = event.node;
                

                $("#device_desc").load('<?= Url::toRoute('device/view') ?>&id=' + node.id + " .device-view");                    
            }
        );

        //prawy przycisk dla węzła
        $('#device_tree').bind(
            'tree.contextmenu',
            function(event) {
                // The clicked node is 'event.node'
                var node = event.node;
                //oryginalnie kliknięte event
                var event = event.click_event;

                //$("#device_menu a").attr("href", "http://localhost/wis/backend/web/index.php?r=device%2Fupdate&id=" + node.id);

                var scrollTop = $(window).scrollTop();
                var scrollLeft = $(window).scrollLeft();
                
                var source = [                   
                    { html: "<a href='<?= Url::to(['device/update'])?>&id=" + node.id + "'>Edycja</a>" },
                    { label: "Magazyn" },
                    { label: "Zamień" },
                ];
                
                $("#device_menu").jqxMenu({ source: source });
                
                $("#device_menu").jqxMenu('open', parseInt(event.clientX) + 5 + scrollLeft, parseInt(event.clientY) + 5 + scrollTop);
                return false;   
            }   
        );

        // disable the default browser's context menu.
        $(document).on('contextmenu', function (e) {
            return false;
        }); 
        
        $(document).on(
            "click",
            "li.jqx-item > a",
            function(event) {
                
                //alert('dupa');

                $("#device_desc").load($(this).attr('href') + " .device-update"); 
                
                return false;
                //event.preventDefault();
            }
        );
    });
    
</script>


