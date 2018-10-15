<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


$this->title = 'Edit ' . $user_data['username'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>



    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'registration-form', 'method'=>'post']); ?>


            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'value'=>$user_data['username'],]) ?>
            <?= $form->field($model, 'surname')->textInput(['autofocus' => true, 'value'=>$user_data['surname'],]) ?>
            <?= $form->field($model, 'password')->textInput(['autofocus' => true, 'type'=>'password']) ?>
            <?= $form->field($model, 'phone')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'value'=>$user_data['email'],]) ?>

            <?= $form->field($model, 'role')->textInput(['autofocus' => true, 'value'=>$user_data['role'],]) ?>
            <?= $form->field($model, 'viber')->textInput(['autofocus' => true, 'value'=>$user_data['viber'],]) ?>
            <?= $form->field($model, 'country')->textInput(['autofocus' => true, 'value'=>$user_data['country'],]) ?>
            <?= $form->field($model, 'city')->textInput(['autofocus' => true, 'value'=>$user_data['city'],]) ?>
            <?= $form->field($model, 'address')->textInput(['autofocus' => true, 'value'=>$user_data['address'],]) ?>
            <?= $form->field($model, 'communication_with_the_operator')->textInput(['autofocus' => true, 'value'=>$user_data['communication_with_the_operator'],]) ?>
            <?= $form->field($model, 'company_name')->textInput(['autofocus' => true, 'value'=>$user_data['company_name'],]) ?>




            <div class="form-group">
                <?= Html::submitButton('Edit', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
