<?php

use yii\db\Migration;

/**
 * Class m190513_064559_move_data
 */
class m190513_064701_add_task_category_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        $this->dropForeignKey('task_type_fkey', 'task');
        $this->dropTable('task_type');
        $this->dropForeignKey('task_category_fkey', 'task');
        $this->dropTable('task_category');
        
        $this->createTable('task_category', [
            'id' => 'serial', 
            'name' => $this->string(50), 
            'parent_id' => $this->integer(),
            'for_subscriber' => $this->boolean(),
        ]);
        $this->addPrimaryKey('task_category_pkey', 'task_category', 'id');
        
        $this->insert('task_category', ['id' => 1, 'name' => 'Urządzenie', 'parent_id' => 0, 'for_subscriber' => false]);
        $this->insert('task_category', ['id' => 2, 'name' => 'Podłączenie', 'parent_id' => 0, 'for_subscriber' => true]);
        $this->insert('task_category', ['id' => 3, 'name' => 'Montaż', 'parent_id' => 0, 'for_subscriber' => true]);
        $this->insert('task_category', ['id' => 4, 'name' => 'Konfiguracja', 'parent_id' => 0, 'for_subscriber' => true]);
        $this->insert('task_category', ['id' => 5, 'name' => 'Usterka', 'parent_id' => 0, 'for_subscriber' => true]);
        $this->insert('task_category', ['id' => 6, 'name' => 'Odłączenie', 'parent_id' => 0, 'for_subscriber' => true]);
        $this->insert('task_category', ['id' => 7, 'name' => 'Własne', 'parent_id' => 0, 'for_subscriber' => false]);
        $this->insert('task_category', ['id' => 8, 'name' => 'Blokada', 'parent_id' => 0, 'for_subscriber' => false]);

        $this->insert('task_category', ['id' => 10, 'name' => 'Usterka', 'parent_id' => 1]);
        $this->insert('task_category', ['id' => 11, 'name' => 'Wymiana', 'parent_id' => 1]);
        $this->insert('task_category', ['id' => 12, 'name' => 'Brudny klosz', 'parent_id' => 1]);
        $this->insert('task_category', ['id' => 13, 'name' => 'Zaparowana', 'parent_id' => 1]);

        $this->insert('task_category', ['id' => 14, 'name' => 'Internet', 'parent_id' => 2]);
        $this->insert('task_category', ['id' => 15, 'name' => 'Telefon', 'parent_id' => 2]);
        $this->insert('task_category', ['id' => 16, 'name' => 'Telewizja', 'parent_id' => 2]);
        $this->insert('task_category', ['id' => 17, 'name' => 'Mobilny', 'parent_id' => 2]);

        $this->insert('task_category', ['id' => 18, 'name' => 'UTP', 'parent_id' => 3]);
        $this->insert('task_category', ['id' => 19, 'name' => 'UTP cat.3', 'parent_id' => 3]);
        $this->insert('task_category', ['id' => 20, 'name' => 'Coax', 'parent_id' => 3]);
        $this->insert('task_category', ['id' => 21, 'name' => 'Światłowód', 'parent_id' => 3]);
        $this->insert('task_category', ['id' => 22, 'name' => 'Inne', 'parent_id' => 3]);

        $this->insert('task_category', ['id' => 23, 'name' => 'Internet', 'parent_id' => 4]);
        $this->insert('task_category', ['id' => 24, 'name' => 'Telefon', 'parent_id' => 4]);
        $this->insert('task_category', ['id' => 25, 'name' => 'Telewizja', 'parent_id' => 4]);
        $this->insert('task_category', ['id' => 26, 'name' => 'Mobilny', 'parent_id' => 4]);

        $this->insert('task_category', ['id' => 27, 'name' => 'Internet', 'parent_id' => 5]);
        $this->insert('task_category', ['id' => 28, 'name' => 'Telefon', 'parent_id' => 5]);
        $this->insert('task_category', ['id' => 29, 'name' => 'Telewizja', 'parent_id' => 5]);
        $this->insert('task_category', ['id' => 30, 'name' => 'Mobilny', 'parent_id' => 5]);

        $this->insert('task_category', ['id' => 31, 'name' => 'Internet', 'parent_id' => 6]);
        $this->insert('task_category', ['id' => 32, 'name' => 'Telefon', 'parent_id' => 6]);
        $this->insert('task_category', ['id' => 33, 'name' => 'Telewizja', 'parent_id' => 6]);
        $this->insert('task_category', ['id' => 34, 'name' => 'Mobilny', 'parent_id' => 6]);

        $this->insert('task_category', ['id' => 35, 'name' => 'Standardowy', 'parent_id' => 14]);
        $this->insert('task_category', ['id' => 36, 'name' => '5G', 'parent_id' => 14]);
        $this->insert('task_category', ['id' => 37, 'name' => 'Normalny', 'parent_id' => 15]);
        $this->insert('task_category', ['id' => 38, 'name' => 'Przeniesiony', 'parent_id' => 15]);
        $this->insert('task_category', ['id' => 39, 'name' => 'DVB-C', 'parent_id' => 16]);
        $this->insert('task_category', ['id' => 40, 'name' => 'IPTV', 'parent_id' => 16]);
        $this->insert('task_category', ['id' => 41, 'name' => 'RFoG', 'parent_id' => 16]);

        $this->insert('task_category', ['id' => 42, 'name' => 'Standardowy', 'parent_id' => 23]);
        $this->insert('task_category', ['id' => 43, 'name' => '5G', 'parent_id' => 23]);
        $this->insert('task_category', ['id' => 44, 'name' => 'Normalny', 'parent_id' => 24]);
        $this->insert('task_category', ['id' => 45, 'name' => 'Przeniesiony', 'parent_id' => 24]);
        $this->insert('task_category', ['id' => 46, 'name' => 'DVB-C', 'parent_id' => 25]);
        $this->insert('task_category', ['id' => 47, 'name' => 'IPTV', 'parent_id' => 25]);
        $this->insert('task_category', ['id' => 48, 'name' => 'RFoG', 'parent_id' => 25]);

        $this->insert('task_category', ['id' => 49, 'name' => 'Standardowy', 'parent_id' => 27]);
        $this->insert('task_category', ['id' => 50, 'name' => '5G', 'parent_id' => 27]);
        $this->insert('task_category', ['id' => 51, 'name' => 'Normalny', 'parent_id' => 28]);
        $this->insert('task_category', ['id' => 52, 'name' => 'Przeniesiony', 'parent_id' => 28]);
        $this->insert('task_category', ['id' => 53, 'name' => 'DVB-C', 'parent_id' => 29]);
        $this->insert('task_category', ['id' => 54, 'name' => 'IPTV', 'parent_id' => 29]);
        $this->insert('task_category', ['id' => 55, 'name' => 'RFoG', 'parent_id' => 29]);

        $this->insert('task_category', ['id' => 56, 'name' => 'Standardowy', 'parent_id' => 31]);
        $this->insert('task_category', ['id' => 57, 'name' => '5G', 'parent_id' => 31]);
        $this->insert('task_category', ['id' => 58, 'name' => 'Normalny', 'parent_id' => 32]);
        $this->insert('task_category', ['id' => 59, 'name' => 'Przeniesiony', 'parent_id' => 32]);
        $this->insert('task_category', ['id' => 60, 'name' => 'DVB-C', 'parent_id' => 33]);
        $this->insert('task_category', ['id' => 61, 'name' => 'IPTV', 'parent_id' => 33]);
        $this->insert('task_category', ['id' => 62, 'name' => 'RFoG', 'parent_id' => 33]);
        
        //TODO dodać powiazanie z tabelą task
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
