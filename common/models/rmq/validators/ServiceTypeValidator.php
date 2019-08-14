<?php

namespace common\models\rmq\validators;

use Yii;
use yii\validators\FilterValidator;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yii\validators\Validator;

class ServiceTypeValidator extends Validator {
	private $requiredRules = [
		[
			'index' => 'technology', 'message' => 'technology jest wymagany'
		], [
			'index' => 'service', 'message' => 'service jest wymagany'
		]
	];
	private $integerRules = [
		[
			'index' => 'technology', 'min' => 1, 'max' => 9, 'message' => 'technology musi być liczbą całkowitą'
		], [
			'index' => 'service', 'min' => 1, 'max' => 4, 'message' => 'service musi być liczbą całkowitą'
		]
	];

	public function validateAttribute($model, $attribute) {
		$this->arrayValidation($model, $attribute);
		$this->requiredValidation($model, $attribute);
		$this->stringValidation($model, $attribute);
		$this->trimValidation($model, $attribute);
		$this->upperValidation($model, $attribute);
	}

	private function arrayValidation($model, $attribute) {
		$address = $model->$attribute;

		if (!is_array($address))
			$this->addError($model, $attribute, 'Wartość musi być tablicą');
	}

	private function requiredValidation($model, $attribute) {
		$address = $model->$attribute;
		$requiredValidator = Yii::createObject([
			'class' => RequiredValidator::class
		]);

		foreach ($this->requiredRules as $value) {
			if (!$requiredValidator->validate($address[$value['index']]))
				$this->addError($model, $attribute, $value['message']);
		}
	}

	private function integerValidation($model, $attribute) {
		$address = $model->$attribute;
		$integerValidator = Yii::createObject([
			'class' => NumberValidator::class, 'integerOnly' => true
		]);

		foreach ($this->stringRules as $value) {
			$integerValidator->min = $value['min'];
			$integerValidator->max = $value['max'];
			if (!$integerValidator->validate($address[$value['index']]))
				$this->addError($model, $attribute, $value['message']);
		}
	}

	private function trimValidation($model, $attribute) {
		$address = $model->$attribute;
		$trimValidator = Yii::createObject([
			'class' => FilterValidator::class, 'filter' => 'trim'
		]);

		foreach ($address as $value) {
			$trimValidator->validate($value);
		}
	}

	private function upperValidation($model, $attribute) {
		$address = $model->$attribute;
		$trimValidator = Yii::createObject([
			'class' => FilterValidator::class, 'filter' => 'strtoupper'
		]);

		foreach ($this->upperRules as $value) {
			if ($trimValidator->validate($address[$value['index']]))
				$this->addError($model, $attribute, $value['message']);
		}
	}
}
?>