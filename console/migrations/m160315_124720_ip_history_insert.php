<?php

use yii\db\Migration;
use backend\models\Device;

class m160315_124720_ip_history_insert extends Migration
{
    public function up()
    {
    	
    	$arIpHost = (new \yii\db\Query())
	    	->select(['address', 'ip',])
	    	->from('device')
	    	->leftJoin('ip', 'ip.device = device.id AND ip.main is true')
	    	->where(['type' => 5])
	    	->all();
    	
    	foreach ($arIpHost as $ipHost){
    		
    		$this->insert('history_ip', [
    			"ip" => $ipHost['ip'],
    			"from_date" => date('Y-m-d H:i:s'),
    			"to_date" => null,
    			"address" => $ipHost['address']
    		]);
    	}
    }

    public function down()
    {
        echo 'Nie da się cofnąć zmian';
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
