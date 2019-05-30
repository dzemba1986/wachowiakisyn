<?php

use yii\db\Migration;

/**
 * Class m190513_064559_move_data
 */
class m190513_065302_add_range_table extends Migration {
    
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        
        $this->createTable('range', [
            'id' => 'serial',
            't_woj' => $this->char(2),
            't_pow' => $this->char(2),
            't_gmi' => $this->char(2),
            't_rodz' => $this->char(1),
            't_miasto' => $this->char(7),
            't_ulica' => $this->char(7),
            'ulica_prefix' => $this->string(50),
            'ulica' => $this->string(256)->notNull(),
            'dom' => $this->string(10)->notNull(),
            'dom_szczegol' => $this->string(50)->defaultValue(null),
            'lokal_od' => $this->string(10)->defaultValue(null),
            'lokal_do' => $this->string(10)->defaultValue(null),
            'utp' => $this->smallInteger(),
            'utp_cat3' => $this->smallInteger(),
            'coax' => $this->smallInteger(),
            'optical_fiber' => $this->smallInteger(),
            'net_utp' => $this->smallInteger(),
            'net_optical_fiber' => $this->smallInteger(),
            'netx_utp' => $this->smallInteger(),
            'netx_optical_fiber' => $this->smallInteger(),
            'phone_utp' => $this->smallInteger(),
            'phone_utp_cat3' => $this->smallInteger(),
            'hfc' => $this->smallInteger(),
            'iptv_utp' => $this->smallInteger(),
            'iptv_optical_fiber' => $this->smallInteger(),
            'rfog' => $this->smallInteger(),
            'iptv_net_utp' => $this->smallInteger(),
            'iptv_net_optical_fiber' => $this->smallInteger(),
            'iptv_netx_utp' => $this->smallInteger(),
            'iptv_netx_optical_fiber' => $this->smallInteger(),
            'rfog_net' => $this->smallInteger(),
            'rfog_netx' => $this->smallInteger(),
        ]);
        $this->addPrimaryKey('range_pkey', 'range', 'id');
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
