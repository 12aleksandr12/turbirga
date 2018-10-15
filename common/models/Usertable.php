<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15.10.2018
 * Time: 13:37
 */

namespace common\models;

use yii\db\ActiveRecord;

class Usertable extends ActiveRecord
{

    public static function tableName(){
        return 'user';
    }

}