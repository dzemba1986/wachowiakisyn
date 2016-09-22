<?php

use yii\db\Migration;
use backend\models\DeviceOld;

class m160315_124509_device_switch_bud_table_insert_data extends Migration
{
    public function up()
    {
        //insert budynkowych
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Switch_bud'])->all();
        
        foreach ($devicesOld as $deviceOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => $deviceOld->lokalizacja <> 111 ? true : false,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                'serial' => $deviceOld->modelDeviceSwitchBud->sn ? strtoupper($deviceOld->modelDeviceSwitchBud->sn) : null,
                "desc" => $deviceOld->opis,
                'address' => $deviceOld->lokalizacja <> 111 ? 7024 : null,
                "type" => 2,
                'model' => $deviceOld->modelDeviceSwitchBud->model,
                "manufacturer" => $deviceOld->modelDeviceSwitchBud->producent,
                'distribution' => FALSE,
            ]);
        }
        
        $this->execute("SELECT setval('device_id_seq', (SELECT MAX(id) FROM device))");
    }

    public function down()
    {
        $this->delete('device', ['type' => 2, 'distribution' => false]);
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
