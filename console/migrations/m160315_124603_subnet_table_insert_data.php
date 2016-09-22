<?php

use yii\db\Migration;
use backend\models\DeviceOld;
use backend\models\SubnetOld;

class m160315_124603_subnet_table_insert_data extends Migration
{
    public function up()
    {
        
        //insert bramek
        $subnetsOld = SubnetOld::find()->all();
        
        foreach ($subnetsOld as $subnetOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('subnet', [
                "id" => $subnetOld->id,
                "ip" => $subnetOld->address . '/' . $subnetOld->netmask,
                "desc" => $deviceOld->opis,
                "vlan" => $deviceOld->vlan,
                'dhcp' => is_null($deviceOld->dhcp) || $deviceOld->dhcp == 0 ? false : true,
                "dhcp_group" => function () {
                	if ($deviceOld->dhcp_group == 1)
                		return null;
                	elseif ($deviceOld->dhcp_group == 2)
                		return 1;
                	elseif ($deviceOld->dhcp_group == 3) 
                		return 2;
                }
            ]);
        }
        
        $this->execute("SELECT setval('subnet_id_seq', (SELECT MAX(id) FROM subnet))");
    }

    public function down()
    {
        $this->truncateTable('subnet');
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
