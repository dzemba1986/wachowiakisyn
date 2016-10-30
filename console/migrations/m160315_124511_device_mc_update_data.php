<?php

use yii\db\Migration;
use backend\models\DeviceOld;

class m160315_124511_device_mc_update_data extends Migration
{
    public function up()
    {
        $this->update('device', ['type' => 8], ['or', ['model' => 42], ['model' => 45]]);
        
        $this->update('model', ['type' => 8], ['name' => 'AT-GS2002/SP']);
        $this->update('model', ['type' => 8], ['name' => 'AT-MC1008']);
    }

    public function down()
    {
        echo 'Nie da się cofnąć operacji';
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
