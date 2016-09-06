<?php

use yii\db\Migration;
use backend\models\DeviceOld;

class m160315_124503_device_table_insert_data extends Migration
{
    public function up()
    {
        
        //insert bramek
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Bramka_voip'])->all();
        
        foreach ($devicesOld as $deviceOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => FALSE,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                'serial' => $deviceOld->modelDeviceVoip->sn ? strtoupper($deviceOld->modelDeviceVoip->sn) : null, //zamienić na wielkie litery
                "desc" => $deviceOld->opis,
                //'address' => NULL,
                "type" => 3,
                'model' => $deviceOld->modelDeviceVoip->model,
                "manufacturer" => $deviceOld->modelDeviceVoip->producent,
                //'distribution' => NULL,
            ]);
        }
        
        
        //insert kamer
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Kamera'])->all();
        
        foreach ($devicesOld as $deviceOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => FALSE,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                'serial' => $deviceOld->modelDeviceCamera->sn ? strtoupper($deviceOld->modelDeviceCamera->sn) : null,
                "desc" => $deviceOld->opis,
                //'address' => NULL,
                "type" => 6,
                'model' => $deviceOld->modelDeviceCamera->model,
                "manufacturer" => $deviceOld->modelDeviceCamera->producent,
                //'distribution' => NULL,
            ]);
        }
        
        //insert ruterów
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Router'])->all();
        
        foreach ($devicesOld as $deviceOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => $deviceOld->mac ? $deviceOld->mac : NULL,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac,
                'serial' => $deviceOld->modelDeviceRouter->sn ? strtoupper($deviceOld->modelDeviceRouter->sn) : null,
                "desc" => $deviceOld->opis,
                //'address' => NULL,
                "type" => 1,
                'model' => $deviceOld->modelDeviceRouter->model,
                "manufacturer" => $deviceOld->modelDeviceRouter->producent,
                //'distribution' => NULL,
            ]);
        }
        
        //insert rejonowych
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Switch_rejon'])->all();
        
        foreach ($devicesOld as $deviceOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => FALSE,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                'serial' => $deviceOld->modelDeviceSwitchRejon->sn ? strtoupper($deviceOld->modelDeviceSwitchRejon->sn) : null,
                "desc" => $deviceOld->opis,
                //'address' => NULL,
                "type" => 2,
                'model' => $deviceOld->modelDeviceSwitchRejon->model,
                "manufacturer" => $deviceOld->modelDeviceSwitchRejon->producent,
                'distribution' => TRUE,
            ]);
        }
        
        //insert budynkowych
        $devicesOld = DeviceOld::find()->       
        where(['device_type' => 'Switch_bud'])->all();
        
        foreach ($devicesOld as $deviceOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('device', [
                "id" => $deviceOld->dev_id,
                "status" => FALSE,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                'serial' => $deviceOld->modelDeviceSwitchBud->sn ? strtoupper($deviceOld->modelDeviceSwitchBud->sn) : null,
                "desc" => $deviceOld->opis,
                //'address' => NULL,
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
        echo "m160315_124503_device_table_insert_data cannot be reverted.\n";

        return false;
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
