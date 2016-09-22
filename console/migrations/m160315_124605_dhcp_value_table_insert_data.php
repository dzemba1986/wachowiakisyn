<?php

use yii\db\Migration;
use backend\models\SubnetOld;
use backend\models\DhcpValueOld;

class m160315_124605_dhcp_value_table_insert_data extends Migration
{
    public function up()
    {
        
        //insert bramek
        $dhcpValuesOld = DhcpValueOld::find()->all();
        
        foreach ($dhcpValuesOld as $dhcpValueOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('dhcp_value', [
                //"id" => $subnetOld->id,
                "weight" => $dhcpValueOld->weight,
                "option" => $dhcpValueOld->option,
                "value" => $dhcpValueOld->value,
                'subnet' => $dhcpValueOld->subnet <> 1 ? $dhcpValueOld->subnet : null,
                "dhcp_group" => function () {
                	if ($dhcpValueOld->dhcp_group == 1)
                		return null;
                	elseif ($dhcpValueOld->dhcp_group == 2)
                		return 1;
                	elseif ($dhcpValueOld->dhcp_group == 3) 
                		return 2;
                }
            ]);
        }
        
        $this->execute("SELECT setval('dhcp_value_id_seq', (SELECT MAX(id) FROM dhcp_value))");
    }

    public function down()
    {
        $this->truncateTable('dhcp_value');
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
