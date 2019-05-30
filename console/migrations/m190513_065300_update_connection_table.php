<?php

use yii\db\Migration;

/**
 * Class m190513_064559_move_data
 */
class m190513_065300_update_connection_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {

        //typ - internet, pakiet - IS
        $this->update('connection', ['package1_id' => 4], ['package_id' => 3]);
        //typ - telefon, pakiet - T
        $this->update('connection', ['package1_id' => 6], ['package_id' => 1]);
        //typ - telefon, pakiet - TP
        $this->update('connection', ['package1_id' => 7], ['package_id' => 2]);
        //typ - tv, pakiet - IPTV
        $this->update('connection', ['package1_id' => 9], ['package_id' => 5]);
        //typ - tv, pakiet - RFoG
        $this->update('connection', ['package1_id' => 10], ['package_id' => 6]);
        
        $this->dropColumn('connection', 'package_id');
        $this->renameColumn('connection', 'package1_id', 'package_id');
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
