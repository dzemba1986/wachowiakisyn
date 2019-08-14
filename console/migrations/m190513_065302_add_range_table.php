<?php
use yii\db\Migration;

class m190513_065302_add_range_table extends Migration {

	public function safeUp() {
		$this->createTable('range', [
			'id' => 'serial',
			't_woj' => $this->char(2),
			't_pow' => $this->char(2),
			't_gmi' => $this->char(2),
			't_rodz' => $this->char(1),
			't_miasto' => $this->char(7),
			't_ulica' => $this->char(7),
			'ulica_prefix' => $this->string(50),
			'ulica' => $this->string(256)->notNull(),
			'dom' => $this->string(10)->notNull(),
			'dom_szczegol' => $this->string(50)->defaultValue(null),
			'lokal_od' => $this->string(10)->defaultValue(null),
			'lokal_do' => $this->string(10)->defaultValue(null),
			'utp' => $this->smallInteger(),
			'utp_cat3' => $this->smallInteger(),
			'coax' => $this->smallInteger(),
			'optical_fiber' => $this->smallInteger(),
			'net_1g_utp' => $this->smallInteger(),
			'net_1g_opt' => $this->smallInteger(),
			'net_10g_utp' => $this->smallInteger(),
			'net_10g_opt' => $this->smallInteger(),
			'phone' => $this->smallInteger(),
			'hfc' => $this->smallInteger(),
			'iptv_utp' => $this->smallInteger(),
			'iptv_opt' => $this->smallInteger(),
			'rfog' => $this->smallInteger(),
			'iptv_net_1g_utp' => $this->smallInteger(),
			'iptv_net_1g_opt' => $this->smallInteger(),
			'iptv_net_10g_utp' => $this->smallInteger(),
			'iptv_net_10g_opt' => $this->smallInteger(),
			'rfog_net_1g' => $this->smallInteger(),
			'rfog_net_10g' => $this->smallInteger(),
			'planning' => $this->text(),	
		]);
		$this->addPrimaryKey('range_pkey', 'range', 'id');
	}

	public function safeDown() {
		echo "m190513_064559_move_data cannot be reverted.\n";

		return false;
	}
}
