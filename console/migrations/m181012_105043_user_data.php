<?php

use yii\db\Migration;
use common\models\Role;
/**
 * Class m181012_105043_user_data
 */
class m181012_105043_user_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_data',
            [
                'id' => $this->primaryKey(),
                'username' => $this->string(),
                'surname' => $this->string(),
                'password' => $this->string(),
                'phone' => $this->string(),
                'email' => $this->string(),
                'role' => $this->integer()->defaultValue(Role::ROLE_ADMIN),
                'viber' => $this->string(),
                'country' => $this->string(),
                'city' => $this->string(),
                'address' => $this->string(),
                'communication_with_the_operator' => $this->string(),
                'company_name' => $this->string(),

            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_data');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181012_105043_user_data cannot be reverted.\n";

        return false;
    }
    */
}
