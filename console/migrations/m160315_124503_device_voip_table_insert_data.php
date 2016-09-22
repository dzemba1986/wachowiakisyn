<?php

use yii\db\Migration;
use backend\models\DeviceOld;

class m160315_124503_device_voip_table_insert_data extends Migration
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
                "status" => $deviceOld->lokalizacja <> 111 ? true : false,
                "name" => $deviceOld->other_name,
                "mac" => $deviceOld->mac ? $deviceOld->mac : NULL,
                'serial' => $deviceOld->modelDeviceVoip->sn ? strtoupper($deviceOld->modelDeviceVoip->sn) : null, //zamienić na wielkie litery
                "desc" => $deviceOld->opis,
                'address' => $deviceOld->lokalizacja <> 111 ? 7024 : null,
                "type" => 3,
                'model' => $deviceOld->modelDeviceVoip->model,
                "manufacturer" => $deviceOld->modelDeviceVoip->producent,
                //'distribution' => NULL,
            ]);
        }
        
        
        $this->execute("SELECT setval('device_id_seq', (SELECT MAX(id) FROM device))");
    }

    public function down()
    {
        $this->delete('device', ['type' => 3]);
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
