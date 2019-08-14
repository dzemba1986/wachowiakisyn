<?php
use yii\db\Migration;

class m190513_064659_add_column_task_table extends Migration {

	public function safeUp() {
		$this->addColumn('task', 'status1', 'smallint');
		$this->addColumn('task', 'type1_id', 'integer');
		$this->addColumn('task', 'category1_id', 'integer');
		$this->addColumn('task', 'subcategory_id', 'integer');
		$this->addColumn('task', 'receive_by', 'smallint');
		$this->addColumn('task', 'fulfit', 'boolean');
		$this->addColumn('task', 'pay_by', 'smallint');
		$this->addColumn('task', 'wire_at', 'date');
		$this->addColumn('task', 'wire_by', 'string');
		$this->addColumn('task', 'wire_length', 'integer');
		$this->addColumn('task', 'socket_at', 'date');
		$this->addColumn('task', 'socket_by', 'string');
		$this->addColumn('task', 'install', 'boolean');
		$this->addColumn('task', 'install_again', 'boolean');
		$this->addColumn('task', 'exec_from', 'date');
		$this->addColumn('task', 'exec_to', 'date');
		$this->addColumn('task', 'scheduled', 'boolean');
	}

	public function safeDown() {
		echo "m190513_064559_move_data cannot be reverted.\n";

		return false;
	}
}
