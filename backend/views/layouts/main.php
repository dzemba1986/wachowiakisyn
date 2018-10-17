<?php
use backend\assets\AppAsset;
use backend\modules\task\models\DeviceTask;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;


$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxcore.js');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxmenu.js');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/styles/jqx.base.css');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/growl/jquery.growl.js');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/growl/jquery.growl.css');

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this); 

$this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => '<span id="jqxWidget" class="glyphicon glyphicon-th"><span id="jqxMenu"></span></span>',
                'brandUrl' => NULL,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => 'LP', 'url' => ['/connection/index']],
                ['label' => 'SEU', 'url' => ['/tree/index'], 'items' => [
                    ['label' => 'Ethernet', 'url' => ['/tree/index']],
                    ['label' => 'RFoG', 'url' => ['/tree/index2']]
                ]],
            	['label' => 'TASK' . '(' . DeviceTask::getCountOpenTask() . ')', 'url' => ['/task/device-task/index']],
            ];
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {
                $menuItems[] = [
                    'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <span>
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </span>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; Wachowiak&amp;Syn <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<script>
    
$(document).ready(function () {
    
    var source = [
        {"html":"Adresy","items":[
            {"html":"<a href=/backend/index.php?r=address%2Faddress%2Findex>Lista</a>"},
            {"html":"<a href=/backend/index.php?r=address%2Faddress%2Flist>Dodaj ulicę</a>"}
        ]},
        {"html":"Połączenia","items":[
            {"html":"<a href=/backend/index.php?r=soa%2Fconnection%2Findex&mode=todo>Do zrobienia</a>"},
            {"html":"<a href=/backend/index.php?r=soa%2Fconnection%2Findex&mode=all>Wszystkie</a>"}
        ]},
        {"html":"<a href=/backend/index.php?r=soa%2Finstallation%2Findex>Instalacje</a>"},
        {"id":"menu-separator"},
        {"html":"Montaże","items":[
            {"html":"<a href=/backend/index.php?r=crm%2Finstall-task&mode=todo>Niewykonane</a>"},
            {"html":"<a href=/backend/index.php?r=crm%2Finstall-task&mode=close>Wykonane</a>"}
        ]},
        {"html":"Zadania","items":[
            {"html":"<a href=/backend/index.php?r=crm%2Fdevice-task&mode=todo>Niewykonane</a>"},
            {"html":"<a href=/backend/index.php?r=crm%2Fdevice-task&mode=close>Wykonane</a>"}
        ]},
        {"id":"menu-separator"},
        {"html":"Zestawienia","items":[
            {"html":"<a href=/backend/index.php?r=report%2Fraport%2Fconnection>Podłączenia</a>"},
            {"html":"<a href=/backend/index.php?r=report%2raport%2Finstallation>Instalacje</a>"},
            {"html":"<a href=/backend/index.php?r=report%2raport%2Ftask>Montaże</a>"}
        ]},
        {"id":"menu-separator"},
        {"html":"<a href=/backend/index.php?r=seu%2tree%2Findex>Drzewo urządzeń</a>"},
        {"html":"<a href=/backend/index.php?r=seu%2ip%2Fhistory>Historia IP</a>"},
        {"html":"Sieć","items":[
            {"html":"<a href=/backend/index.php?r=seu%2vlan%2Fgrid>Adresacja</a>"},
            {"html":"<a href=/backend/index.php?r=seu%2dhcp%2Findex>DHCP</a>"}
        ]},
        {"html":"<a href=/backend/index.php?r=store%2Findex>Magazyn</a>"},
        
    ]

  //utworzenie menu kontekstowego
    var contextMenu = $("#jqxMenu").jqxMenu({source: source, width: '150px', autoOpenPopup: false, mode: 'popup'});
	
    
    //var controler = '<?= Yii::$app->controller->id ?>';
    
    // open the context menu when the user presses the mouse right button.
    $("#jqxWidget").on('mousedown', function (event) {
        var leftClick = isLeftClick(event) || $.jqx.mobile.isTouchDevice();
        if (leftClick) {
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();
            contextMenu.jqxMenu('open', parseInt(event.clientX) + 5 + scrollLeft, parseInt(event.clientY) + 5 + scrollTop);
            return false;
        }
    });

    function isLeftClick(event) {
        var leftclick;
        if (!event) var event = window.event;
        if (event.which) leftclick = (event.which == 1);
        else if (event.button) leftclick = (event.button == 1);
        return leftclick;
    }

    $("body").bind("DOMNodeInserted", function() {
	    $(this).find("li[id='menu-separator']").attr("class","jqx-menu-item-separator jqx-rc-all");
	});
});
</script>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>