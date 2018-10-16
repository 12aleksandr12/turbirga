<?php
namespace common\models;

//use use;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;


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




}
