<?php

use yii\db\Migration;
use backend\models\VlanOld;

class m160315_124601_vlan_table_insert_data extends Migration
{
    public function up()
    {
        
        //insert bramek
        $vlansOld = VlanOld::find()->all();
        
        foreach ($vlansOld as $vlanOld){ 
        
            //var_dump($addressOld);
            //exit;
            
            $this->insert('vlan', [
                "id" => $vlanOld->vid,
                "desc" => $vlanOld->opis,
            ]);
        }
        
//         $this->execute("SELECT setval('vlan_id_seq', (SELECT MAX(id) FROM vlan))");
    }

    public function down()
    {
        $this->truncateTable('vlan');
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
