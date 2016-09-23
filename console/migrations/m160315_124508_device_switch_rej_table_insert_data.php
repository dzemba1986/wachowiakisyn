<?php

use yii\db\Migration;
use backend\models\DeviceOld;

class m160315_124508_device_switch_rej_table_insert_data extends Migration
{
    public function up()
    {
        
        //insert root'a i centralnego
        
		$this->insert('device', [
        		"id" => 1,
        		"status" => true,
        		"name" => 'ROOT',
        		//mac" => null,
        		//'serial' => $deviceOld->modelDeviceSwitchBud->sn ? strtoupper($deviceOld->modelDeviceSwitchBud->sn) : null,
        		"desc" => '',
        		'address' => 7024,
        		"type" => 9,
        		'model' => 64,
        		//"manufacturer" => $deviceOld->modelDeviceSwitchBud->producent,
        		//'distribution' => FALSE,
        ]);
        
        $this->insert('device', [
        		"id" => 3,
        		"status" => true,
        		"name" => '',
        		"mac" => '00:00:cd:29:e9:ac',
        		'serial' => 'C1JB9800R',
        		"desc" => '',
        		'address' => 7024,
        		"type" => 2,
        		'model' => 5,
        		"manufacturer" => 1,
        		'distribution' => true,
        ]);
        
        
        
        //insert rejonowych
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Switch_rejon'])->all();
        
        foreach ($devicesOld as $deviceOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => $deviceOld->lokalizacja <> 111 ? true : false,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                'serial' => $deviceOld->modelDeviceSwitchRejon->sn ? strtoupper($deviceOld->modelDeviceSwitchRejon->sn) : null,
                "desc" => $deviceOld->opis,
                'address' => $deviceOld->lokalizacja <> 111 ? 7024 : null,
                "type" => 2,
                'model' => $deviceOld->modelDeviceSwitchRejon->model,
                "manufacturer" => $deviceOld->modelDeviceSwitchRejon->producent,
                'distribution' => TRUE,
            ]);
        }
        
        $this->execute("SELECT setval('device_id_seq', (SELECT MAX(id) FROM device))");
    }

    public function down()
    {
        $this->delete('device', ['type' => 2, 'distribution' => true]);
        $this->delete('device', ['type' => 9]);
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
