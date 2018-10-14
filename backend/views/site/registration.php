<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Registration';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to registration</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'registration-form', 'method'=>'post']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'surname')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'phone')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'role')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'viber')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'country')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'city')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'address')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'communication_with_the_operator')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'company_name')->textInput(['autofocus' => true]) ?>


            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
