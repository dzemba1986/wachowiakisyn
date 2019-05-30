<?php

use yii\db\Migration;

/**
 * Class m190513_064559_move_data
 */
class m190513_065059_drop_column_task_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
//         $this->update('task', ['receive_by' => 2], ['type1_id' => 3]);
//         $this->update('task', ['receive_by' => 1], ['type1_id' => 1]);
        $this->dropColumn('task', 'color');
        $this->dropColumn('task', 'class_name');
        $this->dropColumn('task', 'editable');
        $this->dropColumn('task', 'when');
        $this->dropColumn('task', 'paid_psm');
        $this->dropColumn('task', 'device_type');
        //zrobiono kopię status1, type1_id ....
        $this->dropColumn('task', 'status');
        $this->dropColumn('task', 'type_id');
        $this->dropColumn('task', 'category_id');
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
