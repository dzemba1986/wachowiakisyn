<?php

use yii\db\Migration;
use backend\models\DeviceOld;

class m160315_124505_device_server_table_insert_data extends Migration
{
    public function up()
    {
        //insert serwerów
        $devicesOld = DeviceOld::find()->
        where(['device_type' => 'Serwer'])->all();
        
        foreach ($devicesOld as $deviceOld){
        
        	//var_dump($addressOld);
        	//exit;
        
        	$this->insert('device', [
        			"id" => $deviceOld->dev_id,
        			"status" => $deviceOld->lokalizacja <> 111 ? true : false,
        			"name" => $deviceOld->other_name,
        			"mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
        			'serial' => $deviceOld->modelDeviceServer->sn ? strtoupper($deviceOld->modelDeviceServer->sn) : null, //zamienić na wielkie litery
        			"desc" => $deviceOld->opis,
        			'address' => $deviceOld->lokalizacja <> 111 ? 7024 : null,
        			"type" => 4,
        			'model' => $deviceOld->modelDeviceServer->model,
        			"manufacturer" => $deviceOld->modelDeviceServer->producent,
        			//'distribution' => NULL,
        	]);
        }
        
        $this->execute("SELECT setval('device_id_seq', (SELECT MAX(id) FROM device))");
    }

    public function down()
    {
        $this->delete('device', ['type' => 4]);
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
