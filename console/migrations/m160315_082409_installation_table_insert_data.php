<?php

use yii\db\Migration;
use backend\models\InstallationOld;

class m160315_082409_installation_table_insert_data extends Migration
{
    public function up()
    {
       $arTypeMap = [
       
           'net' => 1,
           'phone' => 2,
           'tv' => 3,
           'iptv' => 1,
       ];
       
       $installationsOld = InstallationOld::find()->all();
       $dateValidation = new yii\validators\DateValidator(['format' => 'php:Y-m-d']);
       
       foreach ($installationsOld as $installationOld){ 
       
           $dateTimeValidation = new yii\validators\DateValidator(['format' => 'php:Y-m-d H:i:s']);
           
           if(isset($installationOld->invoiced) and $dateTimeValidation->validate($installationOld->invoiced)){
               $date = new DateTime($installationOld->invoiced);
               //$date->format('Y-m-d');
           }
           else
               $date = NULL;
           
           $this->insert('installation', [
               "id" => $installationOld->installation_id,
               "wire_date" => isset($installationOld->wire_installation_date)      ? 
                   $dateValidation->validate($installationOld->wire_installation_date) ? $installationOld->wire_installation_date : NULL    :   
                   NULL,
               "socket_date" => isset($installationOld->socket_installation_date) ? 
                   $dateValidation->validate($installationOld->socket_installation_date) ? $installationOld->socket_installation_date : NULL :
                   NULL,
               "wire_length" => isset($installationOld->wire_length)   ?   is_int((int)$installationOld->wire_length) ? $installationOld->wire_length : NULL    :   NULL,
               "wire_user" => isset($installationOld->wire_installer) ? $installationOld->wire_installer : NULL,
               'socket_user' => isset($installationOld->socket_installer) ? $installationOld->socket_installer :  NULL,
               'invoice_date' => isset($date) ? 
                   $date->format('Y-m-d') : 
                   NULL,
               'type' => isset($installationOld->type) ? $arTypeMap[$installationOld->type] : NULL, 
               'address' => isset($installationOld->localization) ? $installationOld->localization : NULL,
           ]);
       }
       
       $this->execute("SELECT setval('installation_id_seq', (SELECT MAX(id) FROM installation))");
    }

    public function down()
    {
        echo "m160315_082409_installation_table_insert_data cannot be reverted.\n";

        return false;
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
