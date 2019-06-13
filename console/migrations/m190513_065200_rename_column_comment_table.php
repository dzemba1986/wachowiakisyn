<?php

use yii\db\Migration;

/**
 * Class m190513_064559_move_data
 */
class m190513_065200_rename_column_comment_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        $this->renameColumn('comment', 'create', 'create_at');
        $this->renameColumn('comment', 'description', 'desc');
        $this->renameColumn('comment', 'user_id', 'create_by');
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
