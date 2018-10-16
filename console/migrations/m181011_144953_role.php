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
        $tableName = 'role';
        $this->createTable($tableName,
            [
                'id' => $this->primaryKey(),
                'value' => $this->string(),

            ]);

        $this->insert($tableName, ['value'=>'Супер администратор']);
        $this->insert($tableName, ['value'=>'Турагент']);
        $this->insert($tableName, ['value'=>'Туроператор']);


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
