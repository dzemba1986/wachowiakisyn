<?php

use yii\db\Migration;

/**
 * Class m190513_064559_move_data
 */
class m190513_065301_add_connection_package_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        $this->dropForeignKey('connection_type_fkey', 'connection');
        $this->dropForeignKey('package_type_fkey', 'package');
        $this->dropTable('connection_type');
//         $this->dropForeignKey('connection_package_fkey', 'connection');
        $this->dropTable('package');
        
        $this->createTable('connection_package', [
            'id' => 'serial', 
            'name' => $this->string(50), 
            'parent_id' => $this->integer()
        ]);
        $this->addPrimaryKey('connection_package_pkey', 'connection_package', 'id');
        
        $this->insert('connection_package', ['id' => 1, 'name' => 'Internet', 'parent_id' => 0]);
        $this->insert('connection_package', ['id' => 2, 'name' => 'Telefon', 'parent_id' => 0]);
        $this->insert('connection_package', ['id' => 3, 'name' => 'Telewizja', 'parent_id' => 0]);
        $this->insert('connection_package', ['id' => 4, 'name' => 'IS', 'parent_id' => 1]);
        $this->insert('connection_package', ['id' => 5, 'name' => '5G', 'parent_id' => 1]);
        $this->insert('connection_package', ['id' => 6, 'name' => 'T', 'parent_id' => 2]);
        $this->insert('connection_package', ['id' => 7, 'name' => 'TP', 'parent_id' => 2]);
        $this->insert('connection_package', ['id' => 8, 'name' => 'DVB-C', 'parent_id' => 3]);
        $this->insert('connection_package', ['id' => 9, 'name' => 'IPTV', 'parent_id' => 3]);
        $this->insert('connection_package', ['id' => 10, 'name' => 'RFoG', 'parent_id' => 3]);
        
//TODO         $this->addForeignKey('connection_type_fkey', 'connection', 'type_id', 'connection_package', 'id', 'CASCADE', 'RESTRICT');
//TODO         $this->addForeignKey('connection_package_fkey', 'connection', 'package_id', 'connection_package', 'id', 'CASCADE', 'RESTRICT');
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
