<?php

/**
 * @var \yii\web\View $this
 * @var string $content
 */

use frontend\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

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
                'brandLabel' => 'Serwis WTvK',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
                
                if (Yii::$app->user->isGuest) $menuItems = [];
                else {
                    $monitoring = Yii::$app->user->id == 23 ? true : false;
                    $boa = Yii::$app->user->id == 18 ? true : false;
                    
                    $menuItems = [
                        ['label' => 'LP', 'visible' => !$monitoring, 'items' => [
                            ['label' => 'Umowy do zrobienia', 'url' => ['/soa/connection/index', 'mode' => 'todo']],
                            ['label' => 'Wszystkie umowy', 'url' => ['/soa/connection/index', 'mode' => 'all']],
                            ['label' => 'Umowy niezaksięgowane', 'url' => ['/soa/connection/index', 'mode' => 'noboa']],
                            '<li class="divider"></li>',
                            ['label' => 'Instalacje', 'url' => ['/soa/installation/index']],
                        ]],
                        ['label' => 'SEU', 'visible' => !$monitoring && !$boa, 'items' => [
                            ['label' => 'Ethernet', 'url' => ['/seu/link/index']],
                            ['label' => 'RFoG', 'url' => ['/seu/link/index2']],
                            '<li class="divider"></li>',
                            ['label' => 'Magazyn', 'url' => ['/seu/store/index']],
                        ]],
                        ['label' => 'CRM', 'visible' => !$monitoring && !$boa, 'items' => [
                    	    ['label' => 'Kalendarz', 'url' => ['/crm/task/index']],
                    	    ['label' => 'Usterki', 'url' => ['/crm/client-task/index']],
                    	    ['label' => 'Montaże', 'url' => ['/crm/install-task/index']],
                    	    ['label' => 'Kamery', 'url' => ['/crm/device-task/index']],
                    	    ['label' => 'Serwis', 'url' => ['/crm/serwis-task/index']],
                    	]],
                        ['label' => 'LOG', 'visible' => !$monitoring, 'items' => [
                            ['label' => 'Historia IP', 'url' => ['/history/history/ip']]
                        ]],
                        ['label' => 'Zestawienia', 'visible' => !$monitoring && !$boa, 'items' => [
                            ['label' => 'Konfiguracje', 'url' => ['/report/report/connection']],
                            ['label' => 'Instalacje', 'url' => ['/report/report/installation']],
                            ['label' => 'Montaże', 'url' => ['/report/report/task']],
                        ]],
                        ['label' => 'Kamery', 'visible' => $monitoring, 'url' => ['/crm/device-task/index', 'mode' => 'monitoring']],
                    ];
                }
                
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-left'],
                    'items' => $menuItems,
                ]);
                
                $menuLogin = [];
                if (Yii::$app->user->isGuest) {
                    $menuLogin[] = ['label' => 'Signup', 'url' => ['/site/signup']];
                    $menuLogin[] = ['label' => 'Login', 'url' => ['/site/login']];
                } else {
                    $menuLogin[] = [
                        'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                        'items' => [
                            [
                                'label' => 'Wyloguj',
                                'url' => ['/site/logout'],
                                'linkOptions' => ['data-method' => 'post']
                            ],
                            [
                                'label' => 'Zmień hasło',
                                'url' => ['/site/change-password'],
                            ],
                        ]
                    ];
                }
                
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items' => $menuLogin,
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
        <p class="pull-left"><?= Html::a('v3.0', \Yii::getAlias('@web/changelog.txt')) ?></p>
        <p class="pull-right">&copy; Wachowiak&amp;Syn <?= date('Y') ?></p>
        </div>
    </footer>

<?php
$this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>