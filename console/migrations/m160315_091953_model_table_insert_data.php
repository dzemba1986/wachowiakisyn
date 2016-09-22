<?php

use yii\db\Migration;
use backend\models\ModelOld;

class m160315_091953_model_table_insert_data extends Migration
{
    public function up()
    {
        $modelsOld = ModelOld::find()->all();
        
        $arTypeMap = [
            'Switch_rejon' => 2,
            'Switch_centralny' => 2,
            'Switch_bud' => 2,
            'Serwer' => 4,
            'Router' => 1,
            'Kamera' => 6,
            'Bramka_voip' => 3,
        ];
        
        $arLayerMap = [
            'Switch_rejon' => TRUE,
            'Switch_centralny' => TRUE,
            'Switch_bud' => FALSE,
            'Serwer' => NULL,
            'Router' => NULL,
            'Kamera' => NULL,
            'Bramka_voip' => NULL,
        ];
        
        
        foreach ($modelsOld as $modelOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('model', [
                "id" => $modelOld->id,
                "name" => $modelOld->name,
                "port_count" => $modelOld->port_count,
                "type" => $arTypeMap[$modelOld->device_type],
                "manufacturer" => $modelOld->producent,
                'layer3' => $arLayerMap[$modelOld->device_type],
            	'port' => function ($modelOld){
            		return '{' . str_replace(';', ',', $modelOld->ports) . '}';	
            	}
            ]);
        }
    }

    public function down()
    {
        $this->truncateTable('model');
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
