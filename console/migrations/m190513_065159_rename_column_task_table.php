<?php
use yii\db\Migration;

class m190513_065159_rename_column_task_table extends Migration {

	public function safeUp() {
		$this->renameColumn('task', 'create', 'create_at');
		$this->renameColumn('task', 'installer', 'done_by');
		$this->renameColumn('task', 'description', 'desc');
		$this->renameColumn('task', 'add_user', 'create_by');
		$this->renameColumn('task', 'close_user', 'close_by');
		$this->renameColumn('task', 'close', 'close_at');
		$this->renameColumn('task', 'start', 'start_at');
		$this->renameColumn('task', 'end', 'end_at');
		$this->renameColumn('task', 'close_description', 'close_desc');

		$this->renameColumn('task', 'status1', 'status');
		$this->renameColumn('task', 'type1_id', 'type_id');
		$this->renameColumn('task', 'category1_id', 'category_id');
	}

	public function safeDown() {
		echo "m190513_064559_move_data cannot be reverted.\n";

		return false;
	}
}
