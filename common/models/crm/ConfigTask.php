<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;

class ConfigTask extends Task {
	const TYPE = 4;

	public function rules() {
		return [
			[
				'address_id', 'require'
			]
		];
	}

	public static function find() {
		return new TaskQuery(get_called_class(), [
			'type_id' => self::TYPE, 'columns' => self::columns()
		]);
	}
}