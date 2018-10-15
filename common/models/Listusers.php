<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15.10.2018
 * Time: 9:38
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Listusers extends ActiveRecord
{

    public static function tableName(){
        return 'user_data';
    }



}