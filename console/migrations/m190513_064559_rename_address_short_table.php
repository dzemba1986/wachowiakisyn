<?php
use yii\db\Migration;

class m190513_064559_rename_address_short_table extends Migration {

	public function safeUp() {
		$this->renameTable('address_short', 'teryt');
	}

	public function safeDown() {
		echo "m190513_064559_move_data cannot be reverted.\n";

		return false;
	}
}
