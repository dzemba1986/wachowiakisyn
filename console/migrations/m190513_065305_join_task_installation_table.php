<?php
use common\models\crm\InstallTask;
use common\models\soa\Installation;
use yii\db\Migration;

class m190513_065305_join_task_installation_table extends Migration {

	// trzeba zakomentować behaviors w Task i InstallTask przed rozpoczęciem
	public function safeUp() {

		// wszystkie zakończone instalacje UTP i UTP3 ze statusem "true" na adresach które robimy
		$this->addColumn('installation', 'join', 'boolean');
		$installs = Installation::find()->joinWith('address')->where([
			'and', 
			['is not', 'wire_date', null], 
			['is not', 'socket_date', null],
			['status' => true],
			['type_id' => [1, 2]],
			[
				't_ulica' => [
					'10507', // kutrzeby
					'19232', // Kondratija Rylejewa
					'03269', // czarna rola
					'06618', // hercena
					'13488', // murawa
					'08633', // Kłosowa
					'24508', // Włodarska
					'20294', // Słowiańska
					'26323', // oz
					'17923', // op
					'23990', // oww
					'09439', // ok
					'16636', // opl
					'15776' // pasterska
				]
			]
		])->orderBy('id')->asArray()->all();
			
		foreach ($installs as $install) {
			$task = new InstallTask();
			$task->create_at = $install['wire_date'] . ' 00:00:00';
			$task->address_id = $install['address_id'];
			$task->create_by = 20;
			$task->close_by = 19;
			$task->close_at = $install['socket_date'] . ' 00:00:00';
			$task->start_at = null;
			$task->end_at = null;
			$task->status = 1;
			$task->type_id = 3;
			$task->category_id = $install['type_id'] == 1 ? 18 : 19;
			$task->subcategory_id = null;
			$task->receive_by = 2;
			$task->fulfit = true;
			$task->wire_at = $install['wire_date'];
			$task->wire_by = $install['wire_user'];
			$task->wire_length = $install['wire_length'];
			$task->socket_at = $install['socket_date'];
			$task->socket_by = $install['socket_user'];
			$task->install = true;
			$task->again = false;
			$task->done_by = $install['socket_user'];
			$task->pay_by = 2;
			
			if (!$task->save()) {
				var_dump($task->errors);
				break;
			} else
				echo "Zapisano zdarzenie o id $task->id\n";
		}

		$this->execute("
			UPDATE installation i SET \"join\" = true FROM address a WHERE
			a.id = i.address_id AND 
			socket_date IS NOT NULL AND 
			status IS TRUE AND 
			type_id IN (1,2) 
			AND t_ulica IN ('10507', '19232', '03269', '06618', '13488', '08633', '24508', '20294', '26323', '17923', '23990', '09439', '16636', '15776')
		");

		$installs1 = Installation::find()->joinWith('address')->where([
			'and', 
			['is not', 'wire_date', null],
			['is not', 'socket_date', null],
			['status' => true],
			['type_id' => [1, 2]],
			['t_ulica' => '24263'], // wilczak
			['or', 'dom' => '13', 'dom' => '9']
		])->orderBy('id')->asArray()->all();

		foreach ($installs1 as $install) {
			$task = new InstallTask();
			$task->create_at = $install['wire_date'] . ' 00:00:00';
			$task->address_id = $install['address_id'];
			$task->create_by = 20;
			$task->close_by = 19;
			$task->close_at = $install['socket_date'] . ' 00:00:00';
			$task->start_at = null;
			$task->end_at = null;
			$task->status = 1;
			$task->type_id = 3;
			$task->category_id = $install['type_id'] == 1 ? 18 : 19;
			$task->subcategory_id = null;
			$task->receive_by = 2;
			$task->fulfit = true;
			$task->wire_at = $install['wire_date'];
			$task->wire_by = $install['wire_user'];
			$task->wire_length = $install['wire_length'];
			$task->socket_at = $install['socket_date'];
			$task->socket_by = $install['socket_user'];
			$task->install = true;
			$task->install_again = false;
			$task->done_by = $install['socket_user'];
			$task->pay_by = 2;
			if (!$task->save()) {
				var_dump($task->errors);
				break;
			} else
				echo "Zapisano zdarzenie o id $task->id\n";
		}

		$this->execute("
			UPDATE installation i SET \"join\" = true FROM address a WHERE
			a.id = i.address_id AND
			socket_date IS NOT NULL AND
			status IS TRUE AND
			type_id IN (1,2) AND
			t_ulica = '24263' AND
			dom = '13'
		");

		$installs2 = Installation::find()->joinWith('address')->where([
			'and', 
			['is not', 'wire_date', null],
			['is not', 'socket_date', null],
			['status' => true],
			['type_id' => [1, 2]],
			['t_ulica' => '13989'], // Naramowicka
			['<>', 'dom', '47']
		])->orderBy('id')->asArray()->all();

		foreach ($installs2 as $install) {
			$task = new InstallTask();
			$task->create_at = $install['wire_date'] . ' 00:00:00';
			$task->address_id = $install['address_id'];
			$task->create_by = 20;
			$task->close_by = 19;
			$task->close_at = $install['socket_date'] . ' 00:00:00';
			$task->start_at = null;
			$task->end_at = null;
			$task->status = 1;
			$task->type_id = 3;
			$task->category_id = $install['type_id'] == 1 ? 18 : 19;
			$task->subcategory_id = null;
			$task->receive_by = 2;
			$task->fulfit = true;
			$task->wire_at = $install['wire_date'];
			$task->wire_by = $install['wire_user'];
			$task->wire_length = $install['wire_length'];
			$task->socket_at = $install['socket_date'];
			$task->socket_by = $install['socket_user'];
			$task->install = true;
			$task->install_again = false;
			$task->done_by = $install['socket_user'];
			$task->pay_by = 2;
			if (!$task->save()) {
				var_dump($task->errors);
				break;
			} else
				echo "Zapisano zdarzenie o id $task->id\n";
		}

		$this->execute("
			UPDATE installation i SET \"join\" = true FROM address a WHERE
			a.id = i.address_id AND
			socket_date IS NOT NULL AND
			status IS TRUE AND
			type_id IN (1,2) AND
			t_ulica = '13989' AND 
			dom <> '47'
		");

		// TODO instalacje niedokończone (rezygnacje przed montażem + aktywne
		// montaże)
		// TODO instalacje na adresach gdzie ich nie robimy - przeróbka na taski
		// "podłączenia" (do zastanowienia czy w ogóle to robić)

		$installs_not_full = Installation::find()->joinWith('address')->where([
			'and', [
				'or', 'wire_date' => null, 'socket_date' => null
			], [
				'status' => true
			], [
				'type_id' => [
					1, 2
				]
			],
			[
				't_ulica' => [
					'10507', // kutrzeby
					'19232', // Kondratija Rylejewa
					'03269', // czarna rola
					'06618', // hercena
					'13488', // murawa
					'08633', // Kłosowa
					'24508', // Włodarska
					'20294', // Słowiańska
					'26323', // oz
					'17923', // op
					'23990', // oww
					'09439', // ok
					'16636', // opl
					'15776' // pasterska
				]
			]
		])->orderBy('id')->asArray()->all();

		foreach ($installs_not_full as $install) {
			$task = new InstallTask();
			$task->create_at = $install['wire_date'] . ' 00:00:00';
			$task->address_id = $install['address_id'];
			$task->create_by = 20;
			$task->close_by = 19;
			$task->close_at = $install['socket_date'] . ' 00:00:00';
			$task->start_at = null;
			$task->end_at = null;
			$task->status = 1;
			$task->type_id = 3;
			$task->category_id = $install['type_id'] == 1 ? 18 : 19;
			$task->subcategory_id = null;
			$task->receive_by = 2;
			$task->fulfit = false;
			$task->wire_at = $install['wire_date'];
			$task->wire_by = $install['wire_user'];
			$task->wire_length = $install['wire_length'];
			$task->socket_at = $install['socket_date'];
			$task->socket_by = $install['socket_user'];
			$task->install = true;
			$task->install_again = false;
			$task->done_by = $install['socket_user'];
			$task->pay_by = 2;
			if (!$task->save()) {
				var_dump($task->errors);
				break;
			} else
				echo "Zapisano zdarzenie o id $task->id\n";
		}

		echo 'Koniec.....';
	}

	/**
	 *
	 * {@inheritdoc}
	 */
	public function safeDown() {
		echo "m190513_064559_move_data cannot be reverted.\n";

		return false;
	}

	/*
	 * // Use up()/down() to run migration code without a transaction.
	 * public function up()
	 * {
	 *
	 * }
	 *
	 * public function down()
	 * {
	 * echo "m190513_064559_move_data cannot be reverted.\n";
	 *
	 * return false;
	 * }
	 */
}
