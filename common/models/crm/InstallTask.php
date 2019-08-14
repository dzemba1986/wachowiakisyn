<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 *
 * @property string $done_by Kto zamyka montaż (dla szczurka)
 * @property string $phone
 * @property float $cost
 * @property integer $pay_by Płatnik: 1 -> klient; 2 -> wtvk
 * @property string $wire_at
 * @property string $wire_by
 * @property integer $wire_lenght
 * @property string $socket_at
 * @property string $socket_by
 * @property boolean $install Czy montaż jest montażem instalacji do umowy
 * @property boolean $again Czy to ponowny montaż instalacji (poprawka)
 */
class InstallTask extends Task {
	const TYPE = 3;
	const CONTROLLER = 'install-task';

	public static function columns() {
		return ArrayHelper::merge(parent::columns(), [
			'wire_at', 'wire_by', 'wire_length', 'socket_at', 'socket_by', 'install', 'again', 'cost', 'pay_by', 'done_by', 'phone'
		]);
	}

	public function rules() {
		return ArrayHelper::merge(parent::rules(), [
			[
				'address_id', 'required', 'message' => 'Wartość wymagana'
			], [
				'category_id', 'required', 'message' => 'Wartość wymagana'
			], [
				'pay_by', 'integer'
			], [
				'pay_by', 'required', 'message' => 'Wartość wymagana'
			], [
				'pay_by', 'default', 'value' => 2
			], [
				'pay_by', 'filter', 'filter' => 'intval'
			], [
				'cost', 'double', 'message' => 'Wartość liczbowa'
			], [
				'cost', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE
			], [
				'done_by', 'string', 'message' => 'Wartość znakowa', 'whenClient' => new JsExpression('function() {return false}')
			], [
				'done_by', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE
			], [
				'phone', 'trim'
			], [
				'phone', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'
			]
		]);
	}

	public function scenarios() {
		$scenarios = parent::scenarios();
		array_push($scenarios[self::SCENARIO_CREATE], 'install', 'again', 'cost', 'pay_by', 'phone');
		array_push($scenarios[self::SCENARIO_UPDATE], 'cost', 'pay_by', 'phone');
		array_push($scenarios[self::SCENARIO_CLOSE], 'wire_at', 'wire_by', 'wire_length', 'socket_at', 'socket_by', 'cost', 'pay_by', 'done_by');

		return $scenarios;
	}

	public function attributeLabels() {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'phone' => 'Telefon', 'cost' => 'Koszt', 'pay_by' => 'Płatnik', 'done_by' => 'Wykonał', 'wire_at' => 'Data kabla',
			'socket_at' => 'Data gniazda', 'wire_length' => 'Długość kabla', 'wire_by' => 'Monter kabla', 'socket_by' => 'Monter gniazda'
		]);
	}

	public static function find() {
		return new TaskQuery(get_called_class(), [
			'type_id' => self::TYPE, 'columns' => self::columns()
		]);
	}
}