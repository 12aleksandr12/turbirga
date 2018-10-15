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
            [['username', 'password', 'email'], 'required'],
            // rememberMe must be a boolean value

            [['surname', 'phone', 'email', 'role', 'viber', 'country', 'city', 'address', 'communication_with_the_operator', 'company_name'], 'trim'],
            // password is validated by validatePassword()

            ['email', 'email'],
        ];
    }



}