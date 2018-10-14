<?php

use yii\db\Migration;

/**
 * Class m181012_073127_country
 */
class m181012_073127_country extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('country',
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
        $this->dropTable('country');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181012_073127_country cannot be reverted.\n";

        return false;
    }
    */
}
