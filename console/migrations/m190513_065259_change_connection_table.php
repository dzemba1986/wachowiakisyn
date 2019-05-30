<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Class m190513_064559_move_data
 */
class m190513_065259_change_connection_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        $this->addColumn('connection', 'create_at', 'timestamp');
        $this->update('connection', ['create_at' => new Expression('"start_date"')], ['is not', 'start_date', null], []);
        $this->renameColumn('connection', 'add_user', 'create_by');
        $this->addColumn('connection', 'wtvk_create_by', 'string');
        $this->renameColumn('connection', 'start_date', 'start_at');
        $this->update('connection', ['start_at' => new Expression('"soa_date"')], ['is not', 'soa_date', null]);
        $this->renameColumn('connection', 'conf_date', 'conf_at');
        $this->renameColumn('connection', 'conf_user', 'conf_by');
        $this->renameColumn('connection', 'pay_date', 'pay_at');
        $this->addColumn('connection', 'pay_by', 'integer');
        $this->renameColumn('connection', 'close_date', 'close_at');
        $this->renameColumn('connection', 'close_user', 'close_by');
        $this->renameColumn('connection', 'phone_date', 'move_phone_at');
        $this->renameColumn('connection', 'info', 'desc');
        $this->renameColumn('connection', 'info_boa', 'desc_boa');
        $this->renameColumn('connection', 'port', 'parent_port');
        $this->renameColumn('connection', 'device_id', 'parent_device_id');
        $this->renameColumn('connection', 'synch_date', 'synch_at');
        $this->addColumn('connection', 'soa_number', 'string');
        $this->addColumn('connection', 'package1_id', 'integer');
        
//         $this->dropColumn('connection', 'wire');
//         $this->dropColumn('connection', 'socket');
//         $this->dropColumn('connection', 'again'); //TODO do ustalenia z Szefem
        $this->dropColumn('connection', 'soa_date');
        $this->dropColumn('connection', 'exec_date');
        $this->dropColumn('connection', 'soa_iptv');
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
