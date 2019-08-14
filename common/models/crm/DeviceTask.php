<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;
use common\models\seu\devices\BusinessDevice;
use common\models\seu\devices\Device;
use yii\helpers\ArrayHelper;

/**
 *
 * @property integer $device_id
 */
class DeviceTask extends Task {
	const TYPE = 1;
	const CONTROLLER = 'device-task';

	public static function columns() {
		return ArrayHelper::merge(parent::columns(), [
			'device_id'
		]);
	}

	public function rules() {
		return ArrayHelper::merge(parent::rules(), [
			[
				'address_id', 'required', 'message' => 'Wartość wymagana'
			], [
				'category_id', 'required', 'message' => 'Wartość wymagana'
			], [
				'device_id', 'integer'
			], [
				'device_id', 'required', 'message' => 'Wartość wymagana'
			]
		]);
	}

	public function scenarios() {
		$scenarios = parent::scenarios();
		array_push($scenarios[self::SCENARIO_CREATE], 'device_id');

		return $scenarios;
	}

	public function attributeLabels() {
		return ArrayHelper::merge(parent::attributeLabels(), [
			'device_id' => 'Urządzenie'
		]);
	}

	public function beforeSave($insert) {
		if (!parent::beforeSave($insert))
			return false;

		if ($insert) {
			$this->address_id = Device::find()->select([
				'address_id'
			])->where([
				'id' => $this->device_id
			])->asArray()->one()['address_id'];
		}

		return true;
	}

	public static function find() {
		return new TaskQuery(get_called_class(), [
			'type_id' => self::TYPE, 'columns' => self::columns()
		]);
	}

	public function getDevice() {
		return $this->hasOne(BusinessDevice::class, [
			'id' => 'device_id'
		]);
	}
}