<?php

namespace common\models\rmq\validators;

use Yii;
use yii\validators\FilterValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\Validator;

class AddressValidator extends Validator {

	private	$requiredRules = [
		['index' => 'teryt_woj', 'message' => 'teryt_woj wymagany'],
		['index' => 'teryt_pow', 'message' => 'teryt_pow wymagany'],
		['index' => 'teryt_gmi', 'message' => 'teryt_gmi wymagana'],
		['index' => 'teryt_rodz', 'message' => 'teryt_rodz wymagany'],
		['index' => 'teryt_miasto', 'message' => 'teryt_miasto wymagane'],
		['index' => 'teryt_ulica', 'message' => 'teryt_ulica wymagana'],
		['index' => 'ulica_prefix', 'message' => 'ulica_prefix wymagany'],
		['index' => 'ulica', 'message' => 'ulica wymagana'],
		['index' => 'dom', 'message' => 'dom wymagany'],
		['index' => 'dom_szczegol', 'message' => 'dom_szczegol wymagany'],
		['index' => 'lokal', 'message' => 'lokal wymagany'],
		['index' => 'lokal_szczegol', 'message' => 'lokal_szczegol wymagany'],
	];
	private $stringRules = [
		['index' => 'teryt_woj', 'min' => 2, 'max' => 2],
		['index' => 'teryt_pow', 'min' => 2, 'max' => 2],
		['index' => 'teryt_gmi', 'min' => 2, 'max' => 2],
		['index' => 'teryt_rodz', 'min' => 1, 'max' => 2],
		['index' => 'teryt_miasto', 'min' => 7, 'max' => 7],
		['index' => 'teryt_ulica', 'min' => 5, 'max' => 7],
		['index' => 'ulica_prefix', 'min' => 1, 'max' => 7],
		['index' => 'ulica', 'min' => 2, 'max' => 255],
		['index' => 'dom', 'min' => 1, 'max' => 10],
		['index' => 'dom_szczegol', 'min' => 1, 'max' => 50],
		['index' => 'lokal', 'min' => 1, 'max' => 10],
		['index' => 'lokal_szczegol', 'min' => 1, 'max' => 50],
	];
	private $upperRules = [
		['index' => 'dom_szczegol', 'message' => 'dom_szczegol - nie można zastosować filtra upper'],
		['index' => 'lokal_szczegol', 'message' => 'lokal_szczegol - nie można zastosować filtra upper'],
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

	private function stringValidation($model, $attribute) {
		$address = $model->$attribute;
		$stringValidator = Yii::createObject([
			'class' => StringValidator::class,
		]);
		
		foreach ($this->stringRules as $value) {
			$stringValidator->min = $value['min'];
			$stringValidator->max = $value['max'];
			if (!$stringValidator->validate($address[$value['index']]))
				$this->addError($model, $attribute, $value['message']);
		}
	}

	private function trimValidation($model, $attribute) {
		$address = $model->$attribute;
		$trimValidator = Yii::createObject([
			'class' => FilterValidator::class,
			'filter' => 'trim'
		]);
		
		foreach ($address as $value) {
			$trimValidator->validate($value);
		}
	}
	
	private function upperValidation($model, $attribute) {
		$address = $model->$attribute;
		$trimValidator = Yii::createObject([
				'class' => FilterValidator::class,
				'filter' => 'strtoupper'
		]);
		
		foreach ($this->upperRules as $value) {
			if ($trimValidator->validate($address[$value['index']]))
				$this->addError($model, $attribute, $value['message']);
		}
	}
}
?>