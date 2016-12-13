<?php

use yii\db\Migration;
use backend\models\Device;
use backend\models\Address;

class m160315_124719_device_address_update_data extends Migration
{
    public function up()
    {
    	
    	$modelDevices = Device::find()->where(['is not', 'address', null])->andWhere(['type' => 5])->orderBy('id')->all();
    	
    	foreach ($modelDevices as $modelDevice){
    		
    		//echo preg_replace('/^((\w){0,})(\s{1})((\w|\s|\[|\]|\/){0,})$/', '$1$4', $modelDevice->name);
    		
    		$this->update('device', ['name' => preg_replace('/^((\w){0,})(\s{1})((\w|\s|\[|\]|\/){0,})$/', '$1$4', $modelDevice->name)], ['id' => $modelDevice->id]);
    	}
    }

    public function down()
    {
        echo 'Nie da się cofnąć zmian';
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
