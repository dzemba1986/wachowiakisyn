<?php

use yii\db\Migration;
use backend\models\IpOld;

class m160315_124606_ip_table_insert_data extends Migration
{
    public function up()
    {
        
        //insert bramek
        $ipsOld = IpOld::find()->all();
        
        foreach ($ipsOld as $ipOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('ip', [
                "ip" => $ipOld->ip,
                "subnet" => $ipOld->podsiec,
                "main" => $ipOld->main == 1 ? true : false,
                "device" => $ipOld->device
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
