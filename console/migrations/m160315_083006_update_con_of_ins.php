<?php

use yii\db\Migration;
use backend\models\Connection;
use backend\models\Task;

class m160315_083006_update_con_of_ins extends Migration
{
    public function up()
    {
//        typy umowy: 1 - net, 2 - phone, 3 - tv
//        pakiet umowy: 1 - tel, 2 - tel_przen, 3 - int_komf, 4 - int_stand, 5 - tv_ip
//        typ installacji: 1 - ethernet, 2 - telefoniczny, 3 - koncentryczny
       
       $conns = \backend\models\Connection::find()->orderBy('start_date')->all();
       
       foreach ($conns as $conn){
           
//        	aktualizacja gniazd i kabli
           $wire_count = \backend\models\Installation::find()->where(['address' => $conn->address])->
                   andWhere(['type' => $conn->type])->andWhere('wire_date is not null')->count();
           
           $socket_count = \backend\models\Installation::find()->where(['address' => $conn->address])->
                   andWhere(['type' => $conn->type])->andWhere('socket_date is not null')->count();
           
           
           $this->update('connection', ['wire' => $wire_count, 'socket' => $socket_count], ['id' => $conn->id]);
           
           
           //sprawdzam czy wszystkie odnosniki w connection pasuja do id w task
           if (!is_object(Task::findOne($conn->task))){
	           	if (!is_null($conn->task)){
	           		$this->update('connection', ['task' => null], ['id' => $conn->id]);
	           	}
           }
           
           //sprawdzam które polaczenia są ponownymi podlaczeniami
           if ($conn->type == 1 && $conn->nocontract == false)
	           if (Connection::find()->where(['address' => $conn->address, 'type' => 1, 'nocontract' => false])->
	           		andWhere(['<', 'start_date', $conn->start_date])->andWhere(['is not', 'conf_date', null])->count() > 0){
	           	
	           		$this->update('connection', ['again' => true], ['id' => $conn->id]);	
	           }
           	
//            sprawdzam czy wszystkie odnosniki w connection pasuja do id w task
//            $modelsConnection = Connection::find()->where(['is not', 'task', null])->andWhere(['<>', 'task', 0])->all();
            
//            foreach ($modelsConnection as $modelConnection){
//            	if (is_object(Task::findOne($modelConnection->task))){
//            		continue;
//            	} else {
//            		$modelConnection->task = null;
//            		$modelConnection->save();
//            	}
//            }
       		    	
           	
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
