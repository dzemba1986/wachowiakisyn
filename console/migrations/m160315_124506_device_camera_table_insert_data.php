<?php

use yii\db\Migration;
use backend\models\DeviceOld;

class m160315_124506_device_camera_table_insert_data extends Migration
{
    public function up()
    {
        
        //insert kamer
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Kamera'])->all();
        
        foreach ($devicesOld as $deviceOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => $deviceOld->lokalizacja <> 111 ? true : false,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                'serial' => $deviceOld->modelDeviceCamera->sn ? strtoupper($deviceOld->modelDeviceCamera->sn) : null,
                "desc" => $deviceOld->opis,
                'address' => $deviceOld->lokalizacja <> 111 ? 7024 : null,
                "type" => 6,
                'model' => $deviceOld->modelDeviceCamera->model,
                "manufacturer" => $deviceOld->modelDeviceCamera->producent,
                //'distribution' => NULL,
            ]);
        }
        
        $this->execute("SELECT setval('device_id_seq', (SELECT MAX(id) FROM device))");
    }

    public function down()
    {
        $this->delete('device', ['type' => 6]);
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
