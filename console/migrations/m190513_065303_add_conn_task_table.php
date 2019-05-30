<?php

use yii\db\Migration;
use common\models\soa\Connection;
use common\models\crm\ConfigTask;

/**
 * Class m190513_064559_move_data
 */
class m190513_065303_add_conn_task_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        $this->createTable('connection_task', [
            'connection_id' => $this->bigInteger(),
            'task_id' => $this->bigInteger(),
        ]);
        
        $this->createIndex('unique_connectiontask', 'connection_task', ['connection_id', 'task_id']);
        $this->addForeignKey('connectiontask_connection_fkey', 'connection_task', 'connection_id', 'connection', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('connectiontask_task_fkey', 'connection_task', 'task_id', 'task', 'id', 'CASCADE', 'RESTRICT');
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
