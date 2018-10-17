<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 16.10.2018
 * Time: 9:27
 */

namespace common\models;

use yii\db\ActiveRecord;

class Role extends ActiveRecord
{

    const ROLE_ADMIN = 1;

    public static function tableName()
    {
        return 'role';
    }

}