<?php

use yii\db\Migration;
use backend\models\Device;
use backend\models\DeviceOld;
use backend\models\Address;

class m160315_124512_device_address_update_data extends Migration
{
    public function up()
    {
    	$arStreetMap = [
    			'01957' => 'Bóżnicza',
    			'03269' => 'Czarna Rola',
    			'09439' => 'Kosmonautów',
    			'12272' => 'Marcelińska',
    			'13631' => 'Na Murawie',
    			'13989' => 'Naramowicka',
    			'15776' => 'Pasterska',
    			'16636' => 'Pod Lipami',
    			'17923' => 'Przyjaźni',
    			'19232' => 'Kondratija Rylejewa',
    			'22907' => 'Towarowa',
    			'23990' => 'Wichrowe Wzgórze',
    			'24263' => 'Wilczak',
    			'26323' => 'Zwycięstwa',
    			'23306' => 'Ugory',
    	];
    	
    	$modelDevices = Device::find()->where(['is not', 'address', null])->andWhere(['<>', 'type', 5])->andWhere(['<>', 'type', 9])->all();
    	
    	foreach ($modelDevices as $modelDevice){
    		
    		$modelDeviceOld = DeviceOld::findOne($modelDevice->id);
    		
    		if (is_object($modelDeviceOld)) {
    			
    			$modelAddress = new Address();
    			
    			$modelAddress->ulica = $arStreetMap[$modelDeviceOld->modelAddressDeviceOld->ulic];
    			$modelAddress->dom = $modelDeviceOld->modelAddressDeviceOld->nr_bloku;
    			$modelAddress->dom_szczegol = $modelDeviceOld->modelAddressDeviceOld->klatka;
    			$modelAddress->lokal = '';
    			$modelAddress->lokal_szczegol = '';
    			$modelAddress->pietro = null;
    			
    			if (preg_match('/^[p]{1}[0-9]{1,}$/', $modelAddress->dom_szczegol))
    				continue;
    			
    			try {
    				if (!$modelAddress->save())
    					throw new Exception('Problem z zapisem adresu');
    				
    				$modelDevice->address = $modelAddress->id;	
    					
    				if (!$modelDevice->save())
    					throw new Exception('Problem z zapisem urządzenia');
    				
    					
    			} catch (\Exception $e) {
    				echo $e->getMessage();
    			}
    		} else {
    			continue;
    		}
    	}
    }

    public function down()
    {
    	$this->update('device', ['address' => 7024], ['is not', 'address', null]);
    	
        echo 'Adresy w tabeli device usunięte na domyslne OP120';
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
