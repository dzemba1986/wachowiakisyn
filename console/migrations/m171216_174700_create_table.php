<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m171216_174700_create_table extends Migration
{
    public function up(){
        
        //address
        $this->createTable('{{address}}', [
            'id' => $this->bigInteger()->unsigned()->notNull() . "DEFAULT nextval('address_id_seq'::regclass)",
            't_woj' => $this->char(2)->notNull(),
            't_pow' => $this->char(2)->notNull(),
            't_gmi' => $this->char(2)->notNull(),
            't_rodz' => $this->char(1)->notNull(),
            't_miasto' => $this->char(7)->notNull(),
            't_ulica' => $this->char(7)->notNull(),
            'ulica_prefix' => $this->string(10)->notNull(),
            'ulica' => $this->string(30)->notNull(),
            'dom' => $this->string(5)->notNull(),
            'dom_szczegol' => $this->string(50)->notNull()->defaultValue(''),
            'lokal' => $this->string(10)->notNull()->defaultValue(''),
            'lokal_szczegol' => $this->string(50)->notNull()->defaultValue(''),
            'pietro' => $this->string(2)->notNull()->defaultValue(''),
            
        ]);
        
        $this->addPrimaryKey('address_pkey', '{{address}}', 'id');
        
        //address_short
        $this->createTable('{{address_short}}', [
            't_woj' => $this->char(2)->notNull(),
            't_pow' => $this->char(2)->notNull(),
            't_gmi' => $this->char(2)->notNull(),
            't_rodz' => $this->char(1)->notNull(),
            't_miasto' => $this->char(7)->notNull(),
            't_ulica' => $this->char(7)->notNull(),
            'ulica_prefix' => $this->string(10)->notNull(),
            'ulica' => $this->string(30)->notNull(),
            'name' => $this->string(30)->notNull(),
            'config' => $this->smallInteger()->notNull()
        ]);
        
        $this->addPrimaryKey('address_short_pkey', '{{address_short}}', ['t_ulica', 't_miasto']);
        
        //agregation
        $this->createTable('{{agregation}}', [
            'device' => $this->bigInteger()->notNull(),
            'port' => $this->smallInteger()->notNull(),
            'parent_device' => $this->bigInteger()->notNull(),
            'parent_port' => $this->smallInteger()->notNull(),
        ]);
        
        $this->addPrimaryKey('agregation_pkey', '{{agregation}}', ['device', 'port']);
        $this->createIndex('parentdevice_parentport_unique', '{{agregation}}', ['parent_device', 'parent_port']);
        
        //device
        $this->createTable('{{device}}', [
            'id' => Schema::TYPE_UBIGPK . "NOT NULL DEFAULT nextval('device_id_seq'::regclass)",
            'status' => $this->boolean(),
            'name' => $this->string(20),
            'proper_name' => $this->string(30),
            'monitoring_name' => $this->string(30),
            'desc' => $this->text(),
            'mac' => "macaddr",
            'serial' => $this->string(50),
            'model' => $this->integer()->unsigned(),
            'manufacturer' => $this->integer()->unsigned(),
            'distribution' => $this->boolean(),
            'address' => $this->bigInteger()->unsigned(),
            'type' => $this->integer()->notNull(),
            'start_date' => "timestamp without time zone",
            
        ]);
        
        //comment
        $this->createTable('{{comment}}', [
            'id' => Schema::TYPE_UBIGPK . "NOT NULL DEFAULT nextval('comment_id_seq'::regclass)",
            'create_date' => "timestamp without time zone NOT NULL", //zmiana nazwy
            'description' => $this->text(),
            'create_user_id' => $this->integer()->notNull(),
            'task_id' => $this->bigInteger(),
            'comment_id' => $this->bigInteger(),
        ]);
        
        $this->addPrimaryKey('comment_pkey', '{{comment}}', 'id');
        
        //connection
        $this->createTable('{{connection}}', [
            'id' => Schema::TYPE_UBIGPK . "NOT NULL DEFAULT nextval('connection_id_seq'::regclass)",
            
            
            'start_date',
            'conf_date',
            'pay_date',
            'phone_date',
            'synch_date',
            'soa_date',
            'close_date',
            
            'add_user',
            'conf_user',
            'close_user',
            
            'phone',
            'phone2',
            'nocontract',
            'vip',
            'port',
            'port',
            'mac',
            'wire',
            'socket',
            'again',
            
            'info',
            'info_boa',
            
            'host_id',
            'device_id',
            'task_id',
            'ara_id' => $this->string(8),
            'soa_id' => $this->bigInteger(),
            'package_id',
            'address_id',
            'type_id',
            'replaced_id',
            'type_id',
//             'create_date' => "timestamp without time zone NOT NULL", //zmiana nazwy
//             'description' => $this->text(),
//             'create_user_id' => $this->integer()->notNull(),
//             'task_id' => $this->bigInteger(),
//             'comment_id' => $this->bigInteger(),
        ]);
        
        $this->addPrimaryKey('comment_pkey', '{{comment}}', 'id');
    }

    public function down()
    {
        echo "m160315_083006_check_connection cannot be reverted.\n";

        return false;
    }
}
