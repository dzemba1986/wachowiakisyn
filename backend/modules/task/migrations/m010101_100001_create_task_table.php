<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * php yii migrate --migrationPath=@backend/modules/task/migrations
 */

// 1. W tabeli `connection` zmieniamy pole `task` na `task_id`
// 2. W tabeli `task` tworzymy pola `start` i `end`
// 3. Updateujemy je łącząc np. update task set "end" = start_date + end_time
// 4. Zmieniamy pola na `address_id`, `type_id`, `category_id`
// 5. Dodanie kolorów (dodanie kolumny `color` w `task_type` + update w tabeli `task`)
// 6. Usuń zbędne kolumny w `task`

class m010101_100001_create_task_table extends Migration
{
	public function up()
	{
		$this->renameColumn('{{%connection}}', 'task', 'task_id');
		
		$this->addColumn('{{%task}}', 'add', 'datatime');
		$this->addColumn('{{%task}}', 'close', 'datatime');
		
		$this->update('{{%task}}', ['start' => new Expression('start_date + start_time')]);
		$this->update('{{%task}}', ['end' => new Expression('start_date + end_time')]);
		
		$this->renameColumn('{{%task}}', 'address', 'address_id');
		$this->renameColumn('{{%task}}', 'type', 'type_id');
		$this->renameColumn('{{%task}}', 'category', 'category_id');
		
		$this->addColumn('{{%task_type}}', 'color', 'string');
		$this->update('{{%task_type}}', ['color' => '#660033'], ['id' => 1]);
		$this->update('{{{%task_type}}', ['color' => '#333399'], ['id' => 2]);
		$this->update('{{%task_type}}', ['color' => '#009900'], ['id' => 3]);
		$this->update('{{%task_type}}', ['color' => '#FF2400'], ['id' => 4]);
		
		
		$this->update('{{%task}}', ['color' => '#660033'], ['type_id' => 1]);
		$this->update('{{%task}}', ['color' => '#333399'], ['type_id' => 2]);
		$this->update('{{%task}}', ['color' => '#009900'], ['type_id' => 3]);
		$this->update('{{%task}}', ['color' => '#FF2400'], ['type_id' => 4]);
		$this->update('{{%task}}', ['color' => '#909090'], ['is not', 'close', null]);
		
		$this->dropColumn('{{%task}}', 'start_date');
		$this->dropColumn('{{%task}}', 'start_time');
		$this->dropColumn('{{%task}}', 'end_date');
		$this->dropColumn('{{%task}}', 'end_time');
		
		$this->createIndex('idx_task_status', '{{%task}}', 'status');
	}
	
	public function down()
	{
		echo 'Operacja nie do cofnięcia';
	}
}
