<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;


$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxcore.js');
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/jqxmenu.js');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/jqwidgets/styles/jqx.base.css');

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
                ['label' => 'SEU', 'url' => ['/tree/index']],
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
        <p class="pull-left">&copy; Wachowiak&Syn <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<script>
    
$(document).ready(function () {
    
    //utworzenie menu kontekstowego
    var contextMenu = $("#jqxMenu").jqxMenu({ width: '150px', autoOpenPopup: false, mode: 'popup'});
    
    var controler = '<?= Yii::$app->controller->id ?>';
    
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

    //pobieranie menu metodą get (json)
    $.ajax({
        url: "<?= Url::toRoute(['site/get-menu']) ?>",
        data: { controler : controler },
        success: function(source){
            $('#jqxMenu').jqxMenu({ source: source});
        },
        dataType: "JSON"
    });
    
});
</script>
