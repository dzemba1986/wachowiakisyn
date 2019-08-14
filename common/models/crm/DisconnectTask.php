<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;
use yii\helpers\ArrayHelper;

/**
 *
 * @property string $installer
 * @property string $phone
 * @property float $cost
 * @property integer $payer
 */
class DisconnectTask extends Task {
	const TYPE = 6;

	public static function columns() {
		return ArrayHelper::merge(parent::columns(), [
			'phone'
		]);
	}

	public function rules() {
		return ArrayHelper::merge(parent::rules(), [
			[
				'phone', 'trim'
			], [
				'phone', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'
			]
		]);
	}

	public function scenarios() {
		$scenarios = parent::scenarios();
		array_push($scenarios[self::SCENARIO_CREATE], 'phone');
		array_push($scenarios[self::SCENARIO_UPDATE], 'phone');

		return $scenarios;
	}

	public function attributeLabels() {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'phone' => 'Telefon'
		]);
	}

	public static function find() {
		return new TaskQuery(get_called_class(), [
			'type_id' => self::TYPE, 'columns' => self::columns()
		]);
	}
}