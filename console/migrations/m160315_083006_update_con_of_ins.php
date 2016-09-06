<?php

use yii\db\Migration;

class m160315_083006_update_con_of_ins extends Migration
{
    public function up()
    {
//        typy umowy: 1 - net, 2 - phone, 3 - tv
//        pakiet umowy: 1 - tel, 2 - tel_przen, 3 - int_komf, 4 - int_stand, 5 - tv_ip
//        typ installacji: 1 - ethernet, 2 - telefoniczny, 3 - koncentryczny
       
       $conns = \backend\models\Connection::find()->all();
       
       foreach ($conns as $conn){
           
           $wire_count = \backend\models\Installation::find()->where(['address' => $conn->address])->
                   andWhere(['type' => $conn->type])->andWhere('wire_date is not null')->count();
           
           $socket_count = \backend\models\Installation::find()->where(['address' => $conn->address])->
                   andWhere(['type' => $conn->type])->andWhere('socket_date is not null')->count();
           
           
           $this->update('connection', ['wire' => $wire_count, 'socket' => $socket_count], ['id' => $conn->id]);
       }
    }

    public function down()
    {
        echo "m160308_211606_update_con_of_ins cannot be reverted.\n";

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
