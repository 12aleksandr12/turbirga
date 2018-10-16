<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\LoginForm;
use common\models\RegistrationForm;
use common\models\UserdataForm;
use common\models\User;
use common\models\Edituser;
use common\models\Role;


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


        if ( $model->load( Yii::$app->request->post()) ) {
            if( $model->validate() ){

                return $model->registUser();

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

        return $this->render('listusers', ['users_data' => $users_data] );
    }

    public function actionEditdeluser()
    {
        $data_get = Yii::$app->request->get();
        $id = $data_get['id'];
        $del = $data_get['del'];

        $model = new Edituser;

        $user_data = Edituser::find()->where(['id' => $id])->asArray()->one();

        $all_roles_bad = Role::find()->asArray()->all();
        $all_roles = array();


        foreach($all_roles_bad as $val){
            $all_roles[$val['id']] = $val['value'];
            if( $val['id']==$user_data['role'] ) $user_data['params'] = ['options'=>'Турагент'];
        }
        $user_data['all_roles'] = $all_roles;

        if( !empty($id) && empty($del) ) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {

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

                $user_data = $user_data_post;
                $user_data['all_roles'] = $all_roles;

            }

            }

        }

        if( !empty($id) && !empty($del) ) {

            User::findOne($id)->delete();
            Edituser::findOne($id)->delete();

            return Yii::$app->response->redirect(['site/listusers']);
        }


        return $this->render('editdeluser', compact('model','user_data') );
    }




}
