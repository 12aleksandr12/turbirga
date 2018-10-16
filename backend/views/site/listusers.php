<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;


$this->title = 'A list of users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>


    <div class="row">
        <div class="col-lg-5">
            <style>
                table{text-align: center;}
                table td{border:1px solid #000;padding: 3px 5px;}
            </style>
            <table width="100%">
                <tr>
                    <td>id</td>
                    <td>username</td>
                    <td>surname</td>
                    <td>phone</td>
                    <td>email</td>
                    <td>country</td>
                    <td>role</td>
                    <td>status</td>

                </tr>
<?

foreach($users_data as $key=>$val){
    echo '<tr>';

    echo '<td>'. $val['id'] .'</td>';
    echo '<td>'. $val['username'] .'</td>';
    echo '<td>'. $val['surname'] .'</td>';
    echo '<td>'. $val['phone'] .'</td>';
    echo '<td>'. $val['email'] .'</td>';
    echo '<td>'. $val['country'] .'</td>';
    echo '<td>'. $val['status']['status_user_name'] .'</td>';
    echo '<td><a style="color:'. $val['status']['color'] .';" title="Change role" href="/site/listusers?id='. $val['id'] .'&change_role='. $val['status']['status_num'] .'">'. $val['status']['role'] .'</a></td>';


    echo '<td><a href="/site/editdeluser?id='. $val['id'] .'">Редактировать</a></td>';
    echo '<td><a href="/site/editdeluser?id='. $val['id'] .'&del=1">Удалить</a></td>';

    echo '</tr>';
}


?>
            </table>
        </div>
    </div>
</div>
