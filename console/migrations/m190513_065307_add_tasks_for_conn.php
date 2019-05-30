<?php

use yii\db\Migration;
use common\models\soa\Connection;
use common\models\crm\ConfigTask;

/**
 * Class m190513_064559_move_data
 */
class m190513_065307_add_tasks_for_conn extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        $connections = Connection::find()->where([
            'and',
            ['nocontract' => false],
            [
                'or',
                ['and', ['or', ['conf_at' => null], ['pay_at' => null]], ['type_id' => [1,3]]],
                ['and', ['pay_at' => null], ['type_id' => 2]]
                
            ],
            ['connection.close_at' => null]
        ])->all();
        
        $conf_map = [
            4 => [23, 42],
            9 => [25, 47],
            10 => [25, 48],
        ];
        $install_map = [
            4 => [23, 42],
            9 => [25, 47],
            10 => [25, 48],
        ];
            
        foreach ($connections as $connection) {

            //brak konfiguracji
            if (!$connection->conf_at) {
                $confTask = new ConfigTask();
                $confTask->create_at = $connection->create_at;
                $confTask->create_by = 20;
                $confTask->status = 0;
//                 $confTask->type_id = $connection->address_id;
                $confTask->category_id = $conf_map[$connection->package_id][0];
                $confTask->subcategory_id = $conf_map[$connection->package_id][1];
                $confTask->receive_by = 1;
                $confTask->address_id = $connection->address_id;
                $confTask->exec_from = $connection->start_at;
                $confTask->exec_to = $connection->start_at;
                
                $connection->link('tasks', $confTask);
            }
        }
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
