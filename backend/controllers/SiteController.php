<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\RegistrationForm;
use common\models\UserdataForm;
use common\models\User;
use common\models\Listusers;
use common\models\Edituser;
use common\models\Usertable;


/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    /*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['registration', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }*/

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
           return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', ['model' => $model,]);
        }
    }


   public function actionRegistration()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegistrationForm();


        //if( Yii::$app->request->post() ) return print_r( Yii::$app->request->post()['RegistrationForm'] );

        if ( $model->load( Yii::$app->request->post()) ) {
            if( $model->validate() ){

                $password_hash = Yii::$app->security->generatePasswordHash( $model->password );

                $user = new User();
                $user->username = $model->username;
                $user->password_hash = $password_hash;
                $user->auth_key = Yii::$app->security->generateRandomString();
                $user->email = $model->email;
                $user->save();

                $user_data = new UserdataForm();
                $user_data->username = $model->username;
                $user_data->surname = $model->username;
                $user_data->password = $password_hash;
                $user_data->phone = $model->phone;
                $user_data->email = $model->email;
                $user_data->role = $model->role;
                $user_data->viber = $model->viber;
                $user_data->country = $model->country;
                $user_data->city = $model->city;
                $user_data->address = $model->address;
                $user_data->communication_with_the_operator = $model->communication_with_the_operator;
                $user_data->company_name = $model->company_name;
                $user_data->save();

                //var_dump( $user );
                Yii::$app->user->login( $user , $model->rememberMe ? 3600 * 24 * 30 : 0);

                return $this->goHome();
            }
            return Yii::$app->response->redirect(['site/registration']);
        }



        return $this->render('registration', ['model' => $model] );

    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionListusers()
    {

        $users_data = Listusers::find()->asArray()->all();
        return $this->render('listusers', ['users_data' => $users_data] );
    }

    public function actionEditdeluser()
    {
        $id = $_GET['id'];
        $del = $_GET['del'];
        $model = new Edituser;

        $user_data = Edituser::find()->where(['id' => $id])->asArray()->one();

        if($id>0 && !$del) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {

                $user_data_post = Yii::$app->request->post()['Edituser'];
                if ($user_data_post['password']) {

                    $password_hash = Yii::$app->security->generatePasswordHash($user_data_post['password']);
                    $user_data_post['password'] = $password_hash;

                } else $user_data_post['password'] = $user_data['password'];

                Usertable::updateAll([
                    'username' => $user_data_post['username'],
                    'password_hash' => $user_data_post['password'],
                    'email' => $user_data_post['email'],
                ], "id = $id");

                Edituser::updateAll([
                    'username' => $user_data_post['username'],
                    'surname' => $user_data_post['surname'],
                    'password' => $user_data_post['password'],
                    'phone' => $user_data_post['phone'],
                    'email' => $user_data_post['email'],
                    'role' => $user_data_post['role'],
                    'viber' => $user_data_post['viber'],
                    'country' => $user_data_post['country'],
                    'city' => $user_data_post['city'],
                    'communication_with_the_operator' => $user_data_post['communication_with_the_operator'],
                    'company_name' => $user_data_post['company_name'],
                ], "id = $id");

                $user_data = $user_data_post;
            }

            }

        }

        if($id>0 && $del>0) {

            Usertable::findOne($id)->delete();
            Edituser::findOne($id)->delete();

            return Yii::$app->response->redirect(['site/listusers']);
        }


        return $this->render('editdeluser', compact('model','user_data') );
    }




}
