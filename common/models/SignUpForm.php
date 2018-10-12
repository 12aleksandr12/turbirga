<?php

namespace app\models;

use app\components\events\SystemUserRegistrationEvent;
use app\helpers\TenantHelper;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class SignUpForm extends Model
{
    public $company;
    public $language;
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $confirm_password;
    public $agree = false;

    private $lang;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->lang = substr(Yii::$app->request->getPreferredLanguage(), 0, 2);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['company', 'language', 'first_name', 'last_name', 'email', 'password', 'confirm_password', 'agree'], 'required'],
            [['company', 'language', 'first_name', 'last_name', 'email'], 'string'],
            [['company','language',  'first_name', 'last_name', 'email'], 'trim'],
            [['password', 'confirm_password'], 'string', 'min' => 6],
            // rememberMe must be a boolean value
            ['agree', 'boolean'],
            ['agree', function ($attribute, $params) {
                if ($this->$attribute != true)
                    $this->addError($attribute, \app\helpers\TranslationHelper::getTranslation('signup_rules_error', $this->lang));
            }],
            ['email', 'email'],
            ['password', function ($attribute, $params) {
                if ($this->password !== $this->confirm_password) {
                    $this->addError($attribute, \app\helpers\TranslationHelper::getTranslation('signup_user_pass_error', $this->lang));
                }
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'company' => 'Company',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'password' => 'Password',
            'confirm_password' => 'Confirm password',
            'agree' => 'Agree'
        ];
    }

    /**
     * @return \app\models\User|array|bool
     * @throws \Exception
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function signUp()
    {
        if ($this->validate()) {
            $model = new User();
            $data = [
                'User' => $this->getAttributes()
            ];

            if (User::checkAuth0User($this->email)) {
                $model->addError('email', \app\helpers\TranslationHelper::getTranslation('signup_user_email_error', $this->lang));

                return $model;
            }

            if ($model->load($data)) {
                $model->setPassword($this->password);
                $transaction = $model->getDb()->beginTransaction();

                if ($model->save()) {
                    if ($auth0_id = $this->createAuth0User()) {

                        $auth = new Auth([
                            'user_id'   => $model->id,
                            'source'    => 'auth0',
                            'source_id' => $auth0_id,
                        ]);

                        if ($auth->save()) {
                            $transaction->commit();
                            $model->current_tenant_id = TenantHelper::createTenant($model, $this->company, $this->language);
                            $model->save();
                            $event = new SystemUserRegistrationEvent($model,$this->password);
                            \Yii::$container->get('systemEmitter')->trigger($event);
                            return $model;
                        }

                        return false;
                    }

                    return false;
                }

                return $model;
            }

            return $model;
        }

        return false;
    }

    private function createAuth0User()
    {
        $client = Yii::$app->auth0->management_api_client;
        $user_data = $client->users->create([
            'user_id' => '',
            'connection' => 'Username-Password-Authentication',
            'email' => $this->email,
            'username' => strstr($this->email, '@', true) . '_' . time(),
            'password' => $this->password,
            'user_metadata' => [
                'company' => $this->company,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name
            ],
            'email_verified' => false,
            'verify_email' => true
        ]);

        return (isset($user_data['user_id']) ? $user_data['user_id'] : false);
    }
}
