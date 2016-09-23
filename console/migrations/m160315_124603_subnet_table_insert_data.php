<?php

use yii\db\Migration;
use backend\models\SubnetOld;

class m160315_124603_subnet_table_insert_data extends Migration
{
    public function up()
    {
        $dhcpGroup = function ($x) {
        	if ($x->dhcp_group == 1)
            	return null;
            elseif ($x->dhcp_group == 2)
            	return 1;
            elseif ($x->dhcp_group == 3) 
            	return 2;
        };
        //insert bramek
        $subnetsOld = SubnetOld::find()->all();
        
        foreach ($subnetsOld as $subnetOld){ 
        
           	if($subnetOld->id == 1)
           		continue;
            
            $this->insert('subnet', [
                "id" => $subnetOld->id,
                "ip" => "$subnetOld->address/$subnetOld->netmask",
                "desc" => $subnetOld->opis,
                "vlan" => $subnetOld->vlan,
                'dhcp' => is_null($subnetOld->dhcp) || $subnetOld->dhcp == 0 ? false : true,
                "dhcp_group" => $dhcpGroup($subnetOld)
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
