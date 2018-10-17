<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

?>
<div class="site-login">
        <div class="col-lg-2 col-md-offset-5" style="margin-top: 100px" >
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['style' => 'border:1px solid black; padding:20px']
            ]); ?>
            
                <?= $form->field($model, 'username', [
                    'inputOptions' => [
                        'placeholder' => 'Login',
                    ]
                ])->label(FALSE); ?>
                
                <?= $form->field($model, 'password', [
                    'inputOptions' => [
                        'placeholder' => 'Hasło',
                    ]
                ])->passwordInput()->label(FALSE) ?>
                
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                
                <div class="text-center">
                    <?= Html::submitButton('Zaloguj', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
                </div>
                
            <?php ActiveForm::end(); ?>
        </div>
</div>
