<?php

use yii\db\Migration;

/**
 * Class m181011_144953_role
 */
class m181011_144953_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('role',
            [
                'id' => $this->primaryKey(),
                'value' => $this->string(),

            ]);





    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('role');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181011_144953_role cannot be reverted.\n";

        return false;
    }
    */
}
