<?php

use yii\db\Migration;
use backend\models\IpOld;
use backend\models\TreeOld;
use backend\models\DeviceOld;

class m160315_124607_agregation_table_insert_data extends Migration
{
    public function up()
    {
        
        //insert bramek
        $treesOld = TreeOld::find()->all();
        
        foreach ($treesOld as $treeOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('tree', [
                "device" => $treeOld->device,
                "port" => function (){
                	
                	$modelDevice = DeviceOld::findOne($treeOld->device);
                	
	                switch ($modelDevice->device_type) {
					    case 'Host':
					        return $treeOld->port;
					    case 'Switch_bud':
					        $arPorts =  explode(';', $modelDevice->modelDeviceSwitchBud->modelModel->ports);
					        return array_search($treeOld->port, $arPorts) + 1; //zwraca index znalezionego portu
					    case 'Switch_rejon':
					       	$arPorts =  explode(';', $modelDevice->modelDeviceSwitchRejon->modelModel->ports);
					        return array_search($treeOld->port, $arPorts) + 1; //zwraca index znalezionego portu
					    case 'Kamera':
					    	$arPorts =  explode(';', $modelDevice->modelDeviceCamera->modelModel->ports);
					        return array_search($treeOld->port, $arPorts) + 1; //zwraca index znalezionego portu
				    	case 'Bramka_voip':
				    		$arPorts =  explode(';', $modelDevice->modelDeviceSwitchBud->modelModel->ports);
					        return array_search($treeOld->port, $arPorts) + 1; //zwraca index znalezionego portu
				    	case 'Router':
				    		$arPorts =  explode(';', $modelDevice->modelDeviceSwitchBud->modelModel->ports);
					        return array_search($treeOld->port, $arPorts) + 1; //zwraca index znalezionego portu
				    	case 'Serwer':
				    		$arPorts =  explode(';', $modelDevice->modelDeviceSwitchBud->modelModel->ports);
					        return array_search($treeOld->port, $arPorts) + 1; //zwraca index znalezionego portu
				    	case 'Virtual':
				    		$arPorts =  explode(';', $modelDevice->modelDeviceSwitchBud->modelModel->ports);
					        return array_search($treeOld->port, $arPorts) + 1; //zwraca index znalezionego portu
					    case 'Switch_centralny':
					        $arPorts =  explode(';', $modelDevice->modelDeviceSwitchBud->modelModel->ports);
					        return array_search($treeOld->port, $arPorts) + 1; //zwraca index znalezionego portu
					}
                	$arPorts = explode(';', DeviceOld::findOne($treeOld->device));
                },
                "parent_device" => $treeOld->parent_device,
                "parent_port" => function (){
                	
                }
            ]);
        }
        
        $this->execute("SELECT setval('ip_id_seq', (SELECT MAX(id) FROM ip))");
    }

    public function down()
    {
        $this->truncateTable('ip');
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
