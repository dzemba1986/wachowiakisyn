<?php

use yii\db\Migration;
use backend\models\Device;
use backend\models\DeviceOld;
use backend\models\Address;

class m160315_124711_device_address_update_data extends Migration
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
    			'11111' => 'Virtual'
    	];
    	
    	$modelDevices = Device::find()->where(['is not', 'address', null])->andWhere(['type' => 5])->all();
    	
    	foreach ($modelDevices as $modelDevice){
    		
    		echo 'Update host o id = ' . $modelDevice->id . "\n";
    		
    		if (empty($modelDevice->name))
    			$this->update('device', ['name' => $modelDevice->modelAddress->fullDeviceShortAddress, 'original_name' => true], ['id' => $modelDevice->id]);
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
