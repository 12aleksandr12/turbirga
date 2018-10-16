<?php
namespace common\models;

//use use;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use common\models\User;

/**
 * Registration Form
 */
class RegistrationForm extends Model
{
    public $username;
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
    public $company_name;

    public $rememberMe = true;

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password', 'email'], 'required'],

            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            [['surname', 'phone', 'email', 'viber', 'country', 'city', 'address', 'communication_with_the_operator', 'company_name'], 'trim'],
            // password is validated by validatePassword()

            ['email', 'email'],
        ];
    }

public function registUser(){
    $password_hash = Yii::$app->security->generatePasswordHash( $this->password );

    $user = new User();
    $user_data = new UserdataForm();

    //$transaction = User::getDb()->beginTransaction();

    $user->username = $this->username;
    $user->password_hash = $password_hash;
    $user->auth_key = Yii::$app->security->generateRandomString();
    $user->email = $this->email;

    if( $user->save() ) {

        $user_data->username = $this->username;
        $user_data->surname = $this->username;
        $user_data->password = $password_hash;
        $user_data->phone = $this->phone;
        $user_data->email = $this->email;
        $user_data->role = 1;
        $user_data->viber = $this->viber;
        $user_data->country = $this->country;
        $user_data->city = $this->city;
        $user_data->address = $this->address;
        $user_data->communication_with_the_operator = $this->communication_with_the_operator;
        $user_data->company_name = $this->company_name;
        $user_data->save();

    }else return Yii::$app->response->redirect(['site/registration']);

    //var_dump( $user );
    Yii::$app->user->login( $user , $this->rememberMe ? 3600 * 24 * 30 : 0);

    return Yii::$app->response->redirect(['site']);

}





}
