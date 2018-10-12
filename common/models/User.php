<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $email
 * @property string $phone
 * @property string $photo
 * @property int $status
 * @property int $last_login
 * @property int $current_tenant_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Auth[] $auths
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    private $fake_tenant;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['current_tenant_id', 'last_login', 'created_at', 'updated_at', 'phone'], 'default', 'value' => null],
            [['status', 'last_login', 'created_at', 'updated_at', 'current_tenant_id'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['auth_key', 'password_hash', 'password_reset_token', 'first_name', 'last_name', 'middle_name', 'photo'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 129],
            ['email', 'email'],
            [['email', 'phone'], 'unique'],
            [['phone'], 'string', 'max' => 32],
//            [['phone'], PhoneInputValidator::className()],
            [['current_tenant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tenants::className(), 'targetAttribute' => ['current_tenant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'middle_name' => 'Middle Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'photo' => 'Photo',
            'status' => 'Status',
            'current_tenant_id' => 'Current tenant',
            'last_login' => 'Last Login',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_admin' => 'Is Admin',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuth()
    {
        return $this->hasOne(Auth::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     *
     * @throws \yii\base\Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $expire = Yii::$app->params['passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * Finds user by auth0 authenticated user
     *
     * @param $auth0Data
     *
     * @return mixed Return null if no matching record
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public static function findByAuth0($auth0Data)
    {
        $query = self::find()
            ->joinWith('auth')
            ->andWhere(['source_id' => $auth0Data['sub']])
            ->andWhere(['source' => 'auth0']);

        if (!$query->exists()) {
            return self::createFromAuth0($auth0Data);
        }

        return $query->one();
    }

    /**
     * @param $auth0Data
     *
     * @return \app\models\User|array|bool|null|\yii\db\ActiveRecord $mixed Return false on error
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public static function createFromAuth0($auth0Data)
    {
        // check is email
        $query = User::find()->andWhere(['email' => $auth0Data['email']]);

        if ($query->exists()) {
            $model = $query->one();

            $auth = new Auth([
                'user_id' => $model->id,
                'source' => 'auth0',
                'source_id' => (string)$auth0Data['sub'],
            ]);

            if ($auth->save()) {
                return $model;
            }

            //$auth->getErrors()

            return false;

        } else {
            $client = \Yii::$app->auth0->management_api_client;
            $auth0Data = $client->users->get($auth0Data['sub']);

            $model = new self([
                'first_name' => (!empty($auth0Data['user_metadata']['first_name']) ? $auth0Data['user_metadata']['first_name'] : $auth0Data['nickname']),
                'last_name' => (!empty($auth0Data['user_metadata']['last_name']) ? $auth0Data['user_metadata']['last_name'] : null),
                'middle_name' => (!empty($auth0Data['user_metadata']['middle_name']) ? $auth0Data['user_metadata']['middle_name'] : null),
                'email' => $auth0Data['email'],
                'phone' => (!empty($auth0Data['user_metadata']['phone']) ? $auth0Data['user_metadata']['phone'] : null)
            ]);

            $model->generateAuthKey();
            $transaction = $model->getDb()->beginTransaction();

            if ($model->save()) {
                $auth = new Auth([
                    'user_id' => $model->id,
                    'source' => 'auth0',
                    'source_id' => (string)$auth0Data['user_id'],
                ]);

                if ($auth->save()) {
                    $transaction->commit();

                    return $model;
                }

                return false;
            }

            return false;

        }
    }

    /**
     * @param $email
     *
     * @return bool
     */
    public static function checkAuth0User($email)
    {
        $client = Yii::$app->auth0->management_api_client;
        $user = $client->usersByEmail->get([
            'email' => $email,
            'fields' => 'user_id'
        ]);

        return empty($user) ? false : true;
    }

    /**
     * @param bool $tenant_id
     *
     * @return bool
     */
    public function checkTenantBlock($tenant_id = false)
    {
        $agent_map = AgentsMapping::findOne(['user_id' => $this->id, 'tenant_id' => ($tenant_id ? $tenant_id : \Yii::$app->user->identity->tenant->id)]);

        if (is_null($agent_map))
            return false;

        return $agent_map->block === true;
    }

    /**
     * @param bool $block
     * $block = true - block user
     * $block = false - unblock user
     *
     * @return bool
     */
    public function blockUser($block = true)
    {
        if (!is_bool($block))
            return false;

        $this->status = $block ? self::STATUS_DELETED : self::STATUS_ACTIVE;

        if ($this->save()) {
            $client = Yii::$app->auth0->management_api_client;
            $client->users->update($this->auth->source_id, [
                'connection' => 'Username-Password-Authentication',
                'blocked' => $block
            ]);

            return true;
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTenants()
    {
        if (Yii::$app->params['stage'] == 'prot')
            return [0 => $this->fake_tenant];

        return $this->hasMany(Tenants::className(), ['id' => 'tenant_id'])
            ->viaTable('agents_mapping', ['user_id' => 'id']);
    }

    public static function getCurrentTenantId()
    {
        $currentTenant = User::find(Yii::$app->user->id)->select('current_tenant_id')->column();
        return $currentTenant[0];
    }

    /**
     * @return ActiveRecord|null|static|bool
     */
    public function getTenant()
    {
        if (Yii::$app->params['stage'] == 'prot')
            return $this->fake_tenant;

        $session = Yii::$app->getSession();
        if ($session->has('cur_tenant')) {
            return $session->get('cur_tenant');
        }
        if (!empty($this->current_tenant_id)) {
            $tenant = Tenants::find()->where(['id' => $this->current_tenant_id])->with('language')->one();
            if (!is_null($tenant)) {
                $session->set('cur_tenant', $tenant);
                return $tenant;
            }
        }

        $user = AgentsMapping::findOne(['user_id' => $this->id]);
        if (!is_null($user)) {
            $tenant = $user->tenant;
            $session->set('cur_tenant', $tenant);
            return $tenant;
        }
        return false;
    }

    public function setTenant(Tenants $tenant)
    {
        $this->fake_tenant = $tenant;
    }

    /**
     * @param $tenant_id
     *
     * @return bool
     */
    public function changeCurrentTenant($tenant_id)
    {
        $tenant = Tenants::findOne($tenant_id);

        if (empty($tenant))
            return false;

        if (!$tenant->checkUser())
            return false;

        $this->current_tenant_id = $tenant_id;

        return true;
    }

    /**
     * @return array
     */
    public function getAssignedItems()
    {
        $items = [];
        $items['contacts'] = Contacts::find()
            ->where(['responsible' => $this->id])
            ->orWhere(['created_by' => $this->id])
            ->andWhere(['tenant_id' => \Yii::$app->user->identity->tenant->id])
            ->count();

        return $items;
    }

    /**
     * @param null $new
     * @param array $tags
     */
    public function reassign($new = null, $tags = [])
    {
        $contacts = Contacts::find()
            ->where(['responsible' => $this->id])
            ->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])
            ->andWhere(['tenant_id' => \Yii::$app->user->identity->tenant->id])
            ->all();

        if (!empty($tags)) {
            if (!is_array($tags))
                $tags = [$tags];
        }

        foreach ($contacts as $contact) {
            if ($contact->responsible == $this->id)
                $contact->responsible = $new;

            if ($contact->created_by == $this->id)
                $contact->created_by = $new;

            if ($contact->updated_by == $this->id)
                $contact->updated_by = $new;

            $contact->save();

            foreach ($tags as $tag) {
                $link = ContactsTags::find()->where(['tag_id' => $tag])->andWhere(['contact_id' => $contact->id])->one();
                if (!is_null($link))
                    continue;
                $link = new ContactsTags();
                $link->contact_id = $contact->id;
                $link->tag_id = $tag;
                $link->save();
            }
        }

        $items = [];
        $items = array_merge($items, Tags::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->andWhere(['tenant_id' => \Yii::$app->user->identity->tenant->id])->all());
        $items = array_merge($items, ContactsTags::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsAddresses::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsSites::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsSocials::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsEmails::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsVisas::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsPassports::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsPhones::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsMessengers::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());
        $items = array_merge($items, ContactsPassports::find()->orWhere(['created_by' => $this->id])
            ->orWhere(['updated_by' => $this->id])->all());

        foreach ($items as $item) {
            if ($item->created_by == $this->id)
                $item->created_by = $new;

            if ($item->updated_by == $this->id)
                $item->updated_by = $new;

            $item->save();
        }
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name;
    }
}
