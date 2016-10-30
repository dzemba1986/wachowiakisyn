<?php

use yii\db\Migration;
use backend\models\IpOld;
use backend\models\TreeOld;
use backend\models\DeviceOld;

class m160315_124607_agregation_table_insert_data extends Migration
{
    public function up()
    {
        $port = function ($x){
                	
        	$modelDevice = DeviceOld::findOne($x->device);
                	
            switch ($modelDevice->device_type) {
				case 'Host':
					return $x->local_port;
				case 'Switch_bud':
			        $arPorts =  explode(';', $modelDevice->modelDeviceSwitchBud->modelModel->ports);
			        return array_search($x->local_port, $arPorts); //zwraca index znalezionego portu
			    case 'Switch_rejon':
			       	$arPorts =  explode(';', $modelDevice->modelDeviceSwitchRejon->modelModel->ports);
			        return array_search($x->local_port, $arPorts); //zwraca index znalezionego portu
				case 'Kamera':
				   	$arPorts =  explode(';', $modelDevice->modelDeviceCamera->modelModel->ports);
				    return array_search($x->local_port, $arPorts); //zwraca index znalezionego portu
			    case 'Bramka_voip':
			    	$arPorts =  explode(';', $modelDevice->modelDeviceVoip->modelModel->ports);
				    return array_search($x->local_port, $arPorts); //zwraca index znalezionego portu
			    case 'Router':
			    	$arPorts =  explode(';', $modelDevice->modelDeviceRouter->modelModel->ports);
				    return array_search($x->local_port, $arPorts); //zwraca index znalezionego portu
			    case 'Serwer':
			    	$arPorts =  explode(';', $modelDevice->modelDeviceServer->modelModel->ports);
				    return array_search($x->local_port, $arPorts); //zwraca index znalezionego portu
			    case 'Virtual':
			    	return $x->local_port;
				case 'Switch_centralny':
				    $arPorts =  explode(';', $modelDevice->modelDeviceSwitchRejon->modelModel->ports);
				    return array_search($x->local_port, $arPorts); //zwraca index znalezionego portu
				}
                
				$arPorts = explode(';', DeviceOld::findOne($x->device));
        };
        
        
        $parent_port = function ($x){
        	 
        	$modelDevice = DeviceOld::findOne($x->parent_device);
        	 
        	switch ($modelDevice->device_type) {
        		case 'Host':
        			return $x->parent_port;
        		case 'Switch_bud':
        			$arPorts =  explode(';', $modelDevice->modelDeviceSwitchBud->modelModel->ports);
        			return array_search($x->parent_port, $arPorts); //zwraca index znalezionego portu
        		case 'Switch_rejon':
        			$arPorts =  explode(';', $modelDevice->modelDeviceSwitchRejon->modelModel->ports);
        			return array_search($x->parent_port, $arPorts); //zwraca index znalezionego portu
        		case 'Kamera':
        			$arPorts =  explode(';', $modelDevice->modelDeviceCamera->modelModel->ports);
        			return array_search($x->parent_port, $arPorts); //zwraca index znalezionego portu
        		case 'Bramka_voip':
        			$arPorts =  explode(';', $modelDevice->modelDeviceVoip->modelModel->ports);
        			return array_search($x->parent_port, $arPorts); //zwraca index znalezionego portu
        		case 'Router':
        			$arPorts =  explode(';', $modelDevice->modelDeviceRouter->modelModel->ports);
        			return array_search($x->parent_port, $arPorts); //zwraca index znalezionego portu
        		case 'Serwer':
        			$arPorts =  explode(';', $modelDevice->modelDeviceServer->modelModel->ports);
        			return array_search($x->parent_port, $arPorts); //zwraca index znalezionego portu
        		case 'Virtual':
        			return $x->parent_port;
        		case 'Switch_centralny':
        			$arPorts =  explode(';', $modelDevice->modelDeviceSwitchRejon->modelModel->ports);
        			return array_search($x->parent_port, $arPorts); //zwraca index znalezionego portu
        	}
        
        	$arPorts = explode(';', DeviceOld::findOne($x->device));
        };
        //insert 
        $this->insert('agregation', ['device' => 3, 'port' => 74, 'parent_device' => 1, 'parent_port' => 1]);
        
        $treesOld = TreeOld::find()->all();
        
        foreach ($treesOld as $treeOld){ 
        
            echo 'Dodaje link ' . $treeOld->device . ' <-> ' . $treeOld->parent_device;
            
            $this->insert('agregation', [
                "device" => $treeOld->device,
                "port" => $port($treeOld),
                "parent_device" => $treeOld->parent_device,
                "parent_port" => $parent_port($treeOld)
            ]);
        }
        
//         $this->execute("SELECT setval('ip_id_seq', (SELECT MAX(id) FROM ip))");
    }

    public function down()
    {
        $this->truncateTable('agregation');
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
