<?php

use common\models\crm\Task;
use common\models\soa\Installation;
use yii\db\Expression;
use yii\db\Migration;
use common\models\crm\InstallTask;

/**
 * Class m190513_064559_move_data
 */
class m190513_065259_join_task_installation_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        //wszystkie zakończone instalacje UTP i UTP3 ze statusem "true" 
        $installs = Installation::find()->where(['is not', 'socket_at', null], ['status' => true], ['type_id' => [1,2]])->asArray()->all();
        
        foreach ($installs as $install) {
            $oldTask = Task::find()->where([
                'type_id' => InstallTask::TYPE,
                'address_id' => $install['address_id'],
                new Expression("TO_CHAR(close_at :: DATE, 'yyyy-mm-dd') = {$install['socket_at']}")
            ])->all();
                
            if ($oldTask && count($oldTask) == 1) {
                $task = $oldTask[0];
                $task->wire_at = $install['wire_at'];
                $task->wire_by = $install['wire_by'];
                $task->wire_length = $install['wire_length'];
                $task->socket_at = $install['socket_at'];
                $task->socket_by = $install['socket_by'];
                $task->reinstall = true;
                $task->install_type = $install['type_id'];
//                 $task->done_by = $install['type_id'];
            } elseif (!$oldTask) {
                $task = new InstallTask();
                $task->create_at = $install['wire_at'] . '00:00:00';
                $task->close_at = $install['socket_at'] . '00:00:00';
                $task->start_at = $install['wire_at'] . '22:00:00';
                $task->end_at = $install['wire_at'] . '23:00:00';
                $task->create_by = 20;
                $task->receive_by = 2;
                $task->close_by = 19;
                $task->address_id = $install['address_id'];
                $task->status = 1;
                $task->type_id = 4;
                $task->category_id = $install['type_id'] == 1 ? 40 : 41;
                $task->subcategory_id = $install['type_id'] == 1 ? 400 : null;
                $task->fulfit = true;
                $task->wire_at = $install['wire_at'];
                $task->wire_by = $install['wire_by'];
                $task->wire_lenght = $install['wire_lenght'];
                $task->socket_at = $install['socket_at'];
                $task->socket_by = $install['socket_by'];
                $task->reinstall = false;
                $task->install_type = $install['type_id'];
                $task->done_by = $install['socket_by'];
            } elseif (count($oldTask) > 1) return 'Znaleciono kilka pasujących montaży';
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
