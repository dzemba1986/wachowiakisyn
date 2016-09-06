<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        //tworzenie tabeli tbl_address
        $this->createTable('{{%address}}', [
            'id' => Schema::TYPE_BIGPK . 'UNSIGNED ',
            't_woj' => Schema::TYPE_STRING . '(2) NOT NULL',
        	't_pow' => Schema::TYPE_STRING . '(2) NOT NULL',
        	't_gmi' => Schema::TYPE_STRING . '(2) NOT NULL',
            't_rodz' => Schema::TYPE_STRING . '(2) NOT NULL',
            't_miasto' => Schema::TYPE_STRING . '(7) NOT NULL',
            't_ulica' => Schema::TYPE_STRING . '(7) NOT NULL',
            'prefix_ulica' => Schema::TYPE_STRING . '(5) NOT NULL',
            'ulica' => Schema::TYPE_STRING . '(20) NOT NULL',
            'dom' => Schema::TYPE_STRING . '(10) NOT NULL',
            'dom_szczegol' => Schema::TYPE_STRING . '(10) NOT NULL',
            'lokal' => Schema::TYPE_STRING . '(10) NOT NULL',
            'lokal_szczegol' => Schema::TYPE_STRING . '(40) NOT NULL',
        ], $tableOptions);
        
        //tworzenie tabeli tbl_address
        $this->createTable('{{%address_ip}}', [
            'id' => Schema::TYPE_BIGPK . 'UNSIGNED',
            'ip' => 'VARBINARY(16) NOT NULL',
        	'main' => Schema::TYPE_BOOLEAN . '(1) UNSIGNED NOT NULL',
        	'subnet' => Schema::TYPE_SMALLINT . '(5) UNSIGNED NOT NULL',
            'device' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',

        ], $tableOptions);
        
        $this->createIndex('ip_subnet_unique', '{{%address_ip}}', ['ip', 'subnet']);
        $this->createIndex('FK_tbl_address_ip_tbl_device', '{{%address_ip}}', 'device', FALSE);
        $this->createIndex('FK_tbl_address_ip_tbl_subnet', '{{%address_ip}}', 'subnet', FALSE);
        
        
		//tworzenie tabeli tbl_user
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
        	'first_name' => Schema::TYPE_STRING . ' NOT NULL',
        	'last_name' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL',

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        
        //tworzenie tabeli tbl_connection
        $this->createTable('{{%connection}}', [
        		'id' => Schema::TYPE_PK,
        		'ara_id' => Schema::TYPE_STRING . ' NOT NULL',
        		'first_name' => Schema::TYPE_STRING . ' NOT NULL',
        		'last_name' => Schema::TYPE_STRING . ' NOT NULL',
        		'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
        		'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
        		'password_reset_token' => Schema::TYPE_STRING,
        		'email' => Schema::TYPE_STRING . ' NOT NULL',
        
        		'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
        		'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        		'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
