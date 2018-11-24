<?php

/**
 * @var \yii\web\View $this
 * @var string $content
 */

use backend\assets\AppAsset;
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
                'brandLabel' => 'Serwis Admin',
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
                    ['label' => 'Adresy', 'visible' => !$monitoring && !$boa, 'items' => [
                        ['label' => 'Obsługiwane adresy', 'url' => ['/address/address/list']],
                        ['label' => 'Adresy', 'url' => ['/address/address/index']],
                    ]],
                    ['label' => 'SEU', 'visible' => !$monitoring && !$boa, 'items' => [
                        ['label' => 'Adresacja', 'url' => ['/seu/vlan/index']],
                    ]],
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
        <p class="pull-left">&copy; Wachowiak&amp;Syn <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>