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
class FailureTask extends Task {
	const TYPE = 5;

	public static function columns() {
		return ArrayHelper::merge(parent::columns(), [
			'phone', 'cost', 'pay_by'
		]);
	}

	public function rules() {
		return ArrayHelper::merge(parent::rules(), [
			[
				'address_id', 'require'
			], [
				'pay_by', 'integer'
			], [
				'pay_by', 'filter', 'filter' => 'intval'
			], [
				'cost', 'double', 'message' => 'Wartość liczbowa'
			], [
				'phone', 'trim'
			], [
				'phone', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Min. {min} znaków', 'tooLong' => 'Max. {max} znaków'
			]
		]);
	}

	public function scenarios() {
		$scenarios = parent::scenarios();
		array_push($scenarios[self::SCENARIO_CREATE], 'phone', 'pay_by', 'cost');
		array_push($scenarios[self::SCENARIO_UPDATE], 'phone', 'pay_by', 'cost');
		array_push($scenarios[self::SCENARIO_CLOSE], 'pay_by', 'cost');

		return $scenarios;
	}

	public function attributeLabels() {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'phone' => 'Telefon', 'cost' => 'Koszt', 'pay_by' => 'Płatnik'
		]);
	}

	public static function find() {
		return new TaskQuery(get_called_class(), [
			'type_id' => self::TYPE, 'columns' => self::columns()
		]);
	}
}