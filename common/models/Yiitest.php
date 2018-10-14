<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12.10.2018
 * Time: 13:25
 */

namespace common\models;

use yii\db\ActiveRecord;


class Yiitest extends ActiveRecord{

    public static function tableName(){
        return 'user';
    }

}