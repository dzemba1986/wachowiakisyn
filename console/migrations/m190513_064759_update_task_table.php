<?php

use yii\db\Migration;

/**
 * Class m190513_064559_move_data
 */
class m190513_064759_update_task_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {

        //aktywne - montaże
        $this->update('task', ['status1' => 0], ['device_id' => null, 'status' => null]);
        //zamknięte - montaże
        $this->update('task', ['status1' => 1], ['and', ['device_id' => null], ['is not', 'status', null]]);
        //typ - montaże
        $this->update('task', ['type1_id' => 3], ['device_id' => null]);
        //typ - montaże, kategoria - UTP
        $this->update('task', ['category1_id' => 18], ['device_id' => null, 'type_id' => 1]);
        //typ - montaże, kategoria - UTP cat.3
        $this->update('task', ['category1_id' => 19], ['device_id' => null, 'type_id' => 2]);
        //typ - montaże, kategoria - Coax
        $this->update('task', ['category1_id' => 20], ['device_id' => null, 'type_id' => 3]);
        //typ - montaże, kategoria - inne
        $this->update('task', ['category1_id' => 22], ['device_id' => null, 'type_id' => 4]);
        //typ - montaże, zrobione
        $this->update('task', ['fulfit' => true], ['device_id' => null, 'status' => true]);
        //typ - montaże, niezrobione
        $this->update('task', ['fulfit' => false], ['device_id' => null, 'status' => false]);
        //typ - montaże, płatność klient
        $this->update('task', ['pay_by' => 1], ['paid_psm' => false]);
        //typ - montaże, płatność WTVK
        $this->update('task', ['pay_by' => 2], ['paid_psm' => true]);
        //typ - montaże, odbiorca
        $this->update('task', ['receive_by' => 2], ['type1_id' => 3]);

        //aktywne - urządzenie
        $this->update('task', ['status1' => 0], ['and', ['is not', 'device_id', null], ['status' => null]]);
        //przyjęte - urządzenie
        $this->update('task', ['status1' => 2], ['and', ['is not', 'device_id', null], ['status' => false]]);
        //zamknięte - urządzenie
        $this->update('task', ['status1' => 1], ['and', ['is not', 'device_id', null], ['status' => true]]);
        //typ - urządzenie
        $this->update('task', ['type1_id' => 1], ['is not', 'device_id', null]);
        //typ - urządzenie, kategoria - usterka
        $this->update('task', ['category1_id' => 10], ['and', ['is not', 'device_id', null], ['type_id' => 5]]);
        //typ - urządzenie, kategoria - do wymiany
        $this->update('task', ['category1_id' => 11], ['and', ['is not', 'device_id', null], ['type_id' => 5], ['status' => false]]);
        //typ - urządzenie, zrobione
        $this->update('task', ['fulfit' => true], ['and', ['is not', 'device_id', null], ['status' => true]]);
        //typ - urządzenia, odbiorca
        $this->update('task', ['receive_by' => 1], ['type1_id' => 1]);

        //czy umówione
        $this->update('task', ['programme' => true], ['and', ['is not', 'start_at', null], ['is not', 'end_at', null]]);
        $this->update('task', ['programme' => false], ['and', ['start_at' => null], ['end_at' => null]]);
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
