<?php
use yii\db\Migration;

class m190513_065059_drop_column_task_table extends Migration {

	public function safeUp() {
		$this->dropColumn('task', 'color');
		$this->dropColumn('task', 'class_name');
		$this->dropColumn('task', 'editable');
		$this->dropColumn('task', 'when');
		$this->dropColumn('task', 'paid_psm');
		$this->dropColumn('task', 'device_type');
		// zrobiono kopię status1, type1_id ....
		$this->dropColumn('task', 'status');
		$this->dropColumn('task', 'type_id');
		$this->dropColumn('task', 'category_id');
	}

	public function safeDown() {
		echo "m190513_064559_move_data cannot be reverted.\n";

		return false;
	}
}
