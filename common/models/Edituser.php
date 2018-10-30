<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15.10.2018
 * Time: 10:28
 */

namespace common\models;
use Yii;
use yii\base\Model;
use common\models\User;
use yii\db\ActiveRecord;

class Edituser extends ActiveRecord
{
    /*public $username;
    public $surname;
    public $password;
    public $phone;
    public $email;
    public $role;
    public $viber;
    public $country;
    public $city;
    public $address;
    public $communication_with_the_operator;
    public $company_name;*/



    public static function tableName(){
        return 'user_data';
    }

    public function rules()
    {
        return [
            // username and password are both required
            [['password'], 'required'],
            // rememberMe must be a boolean value

            [['surname', 'phone', 'email', 'role', 'viber', 'country', 'city', 'address', 'communication_with_the_operator', 'company_name'], 'trim'],
            // password is validated by validatePassword()

            ['email', 'email'],
        ];
    }

    public function editUser()
    {

        $data_get = Yii::$app->request->get();
        $id = $data_get['id'];
        $del = $data_get['del'];

        $user_data = $this::find()->where(['id' => $id])->asArray()->one();

        $all_roles_bad = Role::find()->asArray()->all();
        $all_roles = array();


        $userDataNew = $this::find()
            ->joinWith('roleMethod')
            ->indexBy('id')
            ->all();


        //print_r($userDataNew[73]->roleMethod);

        foreach($all_roles_bad as $val){
            $all_roles[$val['id']] = $val['value'];
            if( $val['id']==$user_data['role'] ) $user_data['params'] = ['options'=>'Турагент'];
        }
        $user_data['all_roles'] = $all_roles;

        if( !empty($id) && empty($del) ) {
            if ($this->load(Yii::$app->request->post())) {
                if ($this->validate()) {

                    $user_data_post = Yii::$app->request->post()['Edituser'];
                    $user_data_post['username'] = $user_data['username'];
                    $user_data_post['email'] = $user_data['email'];

                    //print_r( $user_data_post);
                    if ($user_data_post['password']) {

                        $password_hash = Yii::$app->security->generatePasswordHash($user_data_post['password']);
                        $user_data_post['password'] = $password_hash;

                    } else $user_data_post['password'] = $user_data['password'];

                    User::updateAll([
                        'password_hash' => $user_data_post['password'],
                    ], "id = $id");

/*
                    Edituser::updateAll([
                        'surname' => $user_data_post['surname'],
                        'password' => $user_data_post['password'],
                        'phone' => $user_data_post['phone'],
                        'role' => $user_data_post['role'],
                        'viber' => $user_data_post['viber'],
                        'country' => $user_data_post['country'],
                        'city' => $user_data_post['city'],
                        'communication_with_the_operator' => $user_data_post['communication_with_the_operator'],
                        'company_name' => $user_data_post['company_name'],
                    ], "id = $id");
*/

                    $sendData = ['Edituser'=>$user_data_post];
                    $model = EditUser::find()->where(['id'=>$id])->one();

                    //if( $model->load($sendData) && $model->save() ) echo "Update true";
                    $model->load($sendData);
                    $model->save();

                    $user_data = $user_data_post;
                    $user_data['all_roles'] = $all_roles;

                }

            }

        }

        if( !empty($id) && !empty($del) ) {

            User::findOne($id)->delete();
            Edituser::findOne($id)->delete();

            //return Yii::$app->response->redirect(['site/listusers']);
            return $user_data;
        }
        return $user_data;

    }

    public function getRoleMethod()
    {

        return $this->hasOne(Role::className(), ['id' => 'role']);
    }






}