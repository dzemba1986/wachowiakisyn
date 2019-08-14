<?php
use yii\db\Migration;

class m190513_065200_rename_column_comment_table extends Migration {

	public function safeUp() {
		$this->renameColumn('comment', 'create', 'create_at');
		$this->renameColumn('comment', 'description', 'desc');
		$this->renameColumn('comment', 'user_id', 'create_by');
	}

	public function safeDown() {
		echo "m190513_064559_move_data cannot be reverted.\n";

		return false;
	}
}
