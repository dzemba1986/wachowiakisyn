<?php

use yii\db\Migration;
use backend\models\Connection;
use backend\models\Task;

class m160315_083007_update_connection_of_task extends Migration
{
    public function up()
    {
    	$modelsConnection = Connection::find()->where(['is not', 'task', null])->andWhere(['<>', 'task', 0])->all();
    	
    	foreach ($modelsConnection as $modelConnection){
    		if (is_object(Task::findOne($modelConnection->task))){
    			continue;
    		} else {
    			$modelConnection->task = null;
    			$modelConnection->save();
    		}
    	}
    }

    public function down()
    {
        echo "m160315_083007_update_connection_of_task cannot be reverted.\n";

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
