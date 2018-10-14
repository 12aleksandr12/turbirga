<?php
namespace common\models;

//use use;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;


/**
 * Registration Form
 */
class RegistrationForm extends ActiveRecord
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

    public $rememberMe = true;
    private $_user;

    public static function tableName(){
        return 'user_data';
    }

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            [['surname', 'phone', 'email', 'role', 'viber', 'country', 'city', 'address', 'communication_with_the_operator', 'company_name'], 'trim'],
            // password is validated by validatePassword()

            ['email', 'email'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }


    public function getUser()
    {
        if ($this->_user === null) {
            //$this->save();
            //$this->_user = User::findByUsername($this->username);
            $this->_user = User::find()->where(['username'=>$this->username])->one();
        }

        return $this->_user;
    }

}
