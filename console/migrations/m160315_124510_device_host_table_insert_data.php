<?php

use yii\db\Migration;
use backend\models\DeviceOld;
use backend\models\Connection;

class m160315_124510_device_host_table_insert_data extends Migration
{
    public function up()
    {
        //insert budynkowych
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Host'])->orderBy('dev_id')->all();
        
        $address = function ($x){
        	if(is_object($x->modelDeviceHost->modelConnection))
        		return $x->modelDeviceHost->modelConnection->localization;
        	else 
        		return 7024;
        };
        
        foreach ($devicesOld as $deviceOld){
        	
        	if(is_object(Connection::findOne($deviceOld->modelDeviceHost->con_id))){
        	
        		$modelConnection = Connection::findOne($deviceOld->modelDeviceHost->con_id);
  				$modelConnection->host = $deviceOld->modelDeviceHost->device;
  				$modelConnection->save();
        	} else {
        		null;
        	}
        
            echo 'Dodaje hosta o id = ' . $deviceOld->dev_id;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => $deviceOld->lokalizacja <> 111 ? true : false,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                //'serial' => $deviceOld->modelDeviceSwitchBud->sn ? strtoupper($deviceOld->modelDeviceSwitchBud->sn) : null,
                "desc" => $deviceOld->opis,
                'address' => $address($deviceOld),
                "type" => 5,
                'start' => $deviceOld->modelDeviceHost->data_uruchomienia,
                //"close" => $deviceOld->modelDeviceSwitchBud->producent,
                //'distribution' => FALSE,
            ]);
            
        }
        
        $this->execute("SELECT setval('device_id_seq', (SELECT MAX(id) FROM device))");
    }

    public function down()
    {
        $this->delete('device', ['type' => 5]);
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
