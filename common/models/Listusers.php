<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 17.10.2018
 * Time: 8:58
 */

namespace common\models;

use Yii;

class Listusers
{


    public function listusers()
    {

        $users_data = Edituser::find()->asArray()->all();
        $roles = Role::find()->asArray()->all();
        $data_get = Yii::$app->request->get();
        $id = $data_get['id'];
        $change_role = $data_get['change_role'];

        foreach($users_data as $key=>$val) {

            if( !empty($id) && !empty($change_role) ){
                if( $change_role==10 ){

                    User::updateAll([
                        'status' => User::STATUS_BLOCKED,
                    ], "id = $id");

                }else{

                    User::updateAll([
                        'status' => User::STATUS_ACTIVE,
                    ], "id = $id");

                }

            }
            $status_user = User::findOne($val['id'])['status'];
            $status_user_name = '';

            foreach($roles as $v){
                if( $v['id']==$val['role'] ) $status_user_name = $v['value'];
            }
            if ( $status_user == User::STATUS_ACTIVE ) $users_data[$key]['status'] = ['status_user_name'=>$status_user_name, 'role' => 'Active', 'status_num' => $status_user, 'color' => 'green'];
            if ($status_user == User::STATUS_BLOCKED ) $users_data[$key]['status'] = ['status_user_name'=>$status_user_name, 'role' => 'Blocked', 'status_num' => $status_user, 'color' => 'red'];
        }

        return $users_data;
    }

}