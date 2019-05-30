<?php

/**
 * @var \yii\web\View $this
 * @var string $content
 */

use backend\modules\address\models\Teryt;
use frontend\assets\AppAsset;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
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
                    $menuItems = [
                        [
                            'label' => 'Umowy', 'items' => [
                                [
                                    'label' => 'Otwarte', 
                                    'url' => ['/soa/connection/index', 'mode' => 'todo']
                                ],
                                [
                                    'label' => 'Wszystkie', 
                                    'url' => ['/soa/connection/index', 'mode' => 'all']
                                ],
                                [
                                    'label' => 'Niezaksięgowane', 
                                    'url' => ['/soa/connection/index', 'mode' => 'noboa']
                                ],
//                                 '<li class="divider"></li>',
//                                 [
//                                     'label' => 'Instalacje', 
//                                     'url' => ['/soa/installation/index']
//                                 ],
                            ]
                        ],
                        [
                            'label' => 'CRM ' . Html::tag('span', '', ['id' => 'device-count-task','class' => 'badge', 'style' => 'background-color: red;']), 
                            'items' => [
                        	    [
                        	        'label' => 'Kalendarz', 
                        	        'url' => ['/crm/task/calendar']
                        	    ],
                        	    [
                        	        'label' => 'Zgłoszenia', 
                        	        'url' => ['/crm/task/index?TaskSearch[status][0]=0&TaskSearch[status][1]=2']
                        	    ],
//                         	    [
//                         	        'label' => 'Montaże', 
//                         	        'url' => ['/crm/install-task/index?InstallTaskSearch[status][0]=0']
//                         	    ],
//                                 [
//                                     'label' => 'Kamery', 
//                                     'url' => ['/crm/device-task/index?DeviceTaskSearch[status][0]=0&DeviceTaskSearch[status][1]=2']
//                                 ],
//                         	    [
//                         	        'label' => 'Własne', 
//                         	        'url' => ['/crm/sefl-task/index']
//                         	    ],
                        	]
                        ],
                        [
                            'label' => 'SEU', 'items' => [
                                [
                                    'label' => 'Ethernet', 
                                    'url' => ['/seu/link/index']
                                ],
                                [
                                    'label' => 'RFoG', 
                                    'url' => ['/seu/link/index2']
                                ],
                                '<li class="divider"></li>',
                                [
                                    'label' => 'Magazyn', 
                                    'url' => ['/seu/store/index']
                                ],
                            ]
                        ],
                        [
                            'label' => 'LOG', 'items' => [
                                [
                                    'label' => 'Historia IP', 
                                    'url' => ['/history/history/ip']
                                ]
                            ]
                        ],
                        [
                            'label' => 'Zestawienia', 'items' => [
                                [
                                    'label' => 'Konfiguracje', 
                                    'url' => ['/report/report/connection']
                                ],
                                [
                                    'label' => 'Instalacje', 
                                    'url' => ['/report/report/installation']
                                ],
                                [
                                    'label' => 'Montaże', 
                                    'url' => ['/report/report/task']
                                ],
                            ]
                        ],
                        [
                            'label' => 'Kamery', 
                            'url' => ['/crm/device-task/index', 'mode' => 'monitoring']
                        ],
                    ];
                    
                    
                }
                
                if (Yii::$app->user->isGuest) {
                    $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
                    $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
                } else {
                    $menuItems[] = [
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
                    'options' => ['class' => 'navbar-nav navbar-left'],
                    'items' => $menuItems,
                    'encodeLabels' => false,
                ]);

                if (!Yii::$app->user->isGuest) {
                    ActiveForm::begin([
                        'id' => 'address-search',
                        'options' => ['class' => 'navbar-form navbar-right'],
                        'method' => 'get',
                        'action' => ['/soa/address/index'],
                    ]);
                        
                        echo Html::beginTag('div', ['class' => 'form-group']);
                            echo Select2::widget([
                                'name' => 'AddressSearch[ulica]',
                                'data' => ArrayHelper::map(Teryt::findOrderStreetName(), 't_ulica', 'ulica'),
                                'pluginOptions' => [
                                    'placeholder' => 'Ulica',
                                    'allowClear' => true,
                                    'width' => '220px',
                                ],
                            ]);
                        echo Html::endTag('div');
        
                        echo Html::textInput('AddressSearch[dom]', '', [
                            'placeholder' => 'Dom', 
                            'class' => 'form-control',
                            'style' => 'margin: 0 2px 0 2px',
                        ]);
    
                        echo Html::textInput('AddressSearch[lokal]', '', [
                            'placeholder' => 'Lokal', 
                            'class' => 'form-control',
                            'style' => 'margin: 0 2px 0 0',
                        ]);
                        
                        echo Html::submitButton('<i class="glyphicon glyphicon-search"></i>', ['class' => 'btn btn-danger']);
                    
                    ActiveForm::end();
                }
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
$url = Url::to(['/crm/device-task/get-count-open-task']);
$js = <<<JS
$(function() {
    $.get( '{$url}', function( data ) {
        $( '#device-count-task' ).html( data );
    });   
});
JS;

$this->registerJs($js);
$this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>