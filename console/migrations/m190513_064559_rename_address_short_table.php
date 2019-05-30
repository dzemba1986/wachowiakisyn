<?php

use yii\db\Migration;

/**
 * Class m190513_064559_move_data
 */
class m190513_064559_rename_address_short_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        $this->renameTable('address_short', 'teryt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        
        echo "m190513_064559_move_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190513_064559_move_data cannot be reverted.\n";

        return false;
    }
    */
}
