<?php

namespace common\models\rmq\cases\connect;

use common\models\address\Address;
use common\models\rmq\Job;
use common\models\soa\Connection;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\helpers\ArrayHelper;
use common\models\rmq\validators\AddressValidator;

class ConnectJob extends Job {
	const TYPE = 4;
	public $soa_id;
	public $ara_id;
	public $soa_number;
	public $replaced_id;
	public $service_type;
	public $start_at;
	public $create_by;
	public $exec_from;
	public $exec_to;
	public $phone_at;
	public $address;
	public $phone;
	public $phone2;
	public $desc;
	public $mac;

	/**
	 *
	 * @var integer ID technologii na której ma być świadczona usługa
	 */
	public $technology;

	/**
	 *
	 * @var integer ID usługi/zmiany usługi
	 */
	public $service;

	/**
	 *
	 * @var integer ID typu usługi
	 */
	public $type_id;

	/**
	 *
	 * @var integer ID pakietu usługi
	 */
	public $package_id;

	public function init() {
		parent::init();

		foreach ($this->service_type as $prop => $value) {
			if (property_exists($this, $prop))
				$this->$prop = $value;
		}
		$this->type_id = $this->getType();
		$this->package_id = $this->getPackage();
	}

	public function rules() {
		return [
			[
				'address', AddressValidator::class
			], [
				'soa_id', 'required', 'message' => 'Wartość wymagana'
			], [
				'soa_id', 'integer'
			], [
				'soa_id', 'trim'
			], [
				'ara_id', 'required', 'message' => 'Wartość wymagana'
			], [
				'ara_id', 'string'
			], [
				'soa_id', 'trim'
			], [
				'soa_number', 'required', 'message' => 'Wartość wymagana'
			], [
				'soa_number', 'string'
			], [
				'soa_number', 'trim'
			], [
				'replaced_id', 'integer'
			], [
				'replaced_id', 'trim'
			], [
				'start_at', 'required', 'message' => 'Wartość wymagana'
			], [
				'start_at', 'date', 'format' => 'php:c'
			], [
				'start_at', 'trim'
			], [
				'create_by', 'required', 'message' => 'Wartość wymagana'
			], [
				'create_by', 'string'
			], [
				'create_by', 'trim'
			], [
				'exec_from', 'required', 'message' => 'Wartość wymagana'
			], [
				'exec_from', 'date', 'format' => 'php:c'
			], [
				'exec_from', 'trim'
			], [
				'exec_to', 'required', 'message' => 'Wartość wymagana'
			], [
				'exec_to', 'date', 'format' => 'php:c'
			], [
				'exec_to', 'trim'
			], [
				'phone_at', 'date', 'format' => 'php:c'
			], [
				'phone_at', 'trim'
			], [
				'phone', 'trim'
			], [
				'phone', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Min. {min} znaków', 'tooLong' => 'Max. {max} znaków'
			], [
				'phone2', 'trim'
			], [
				'phone2', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Min. {min} znaków', 'tooLong' => 'Max. {max} znaków'
			], [
				'mac', MacaddressValidator::class, 'message' => 'Zły format'
			], [
				'mac', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => true
			], [
				'technology', 'required', 'message' => 'Wartość wymagana'
			], [
				'technology', 'integer', 'min' => 1, 'max' => 9
			], [
				'service', 'required', 'message' => 'Wartość wymagana'
			], [
				'service', 'integer', 'min' => 1, 'max' => 4
			]
		];
	}

	public function fields() {
		return ArrayHelper::merge(parent::fields(), [
			'soa_id', 'ara_id', 'soa_number', 'replace_id', 'service_type', 'start_at', 'create_by', 'exec_from', 'exec_to', 'move_phone_at',
			'address', 'phone', 'phone2', 'desc', 'mac'
		]);
	}

	private function getAddress() {
		return \Yii::createObject([
			'class' => Address::class,
			't_woj' => $this->address['teryt_woj'],
			't_pow' => $this->address['teryt_pow'],
			't_gmi' => $this->address['teryt_gmi'],
			't_rodz' => $this->address['teryt_rodz'],
			't_miasto' => $this->address['teryt_miasto'],
			't_ulica' => $this->address['teryt_ulica'],
			'ulica_prefix' => $this->address['ulica_prefix'],
			'ulica' => $this->address['ulica'],
			'dom' => $this->address['dom'],
			'dom_szczegol' => $this->address['dom_szczegol'],
			'lokal' => $this->address['lokal'],
			'lokal_szczegol' => $this->address['lokal_szczegol']
		]);
	}

	private function getType() {
		$out = null;
		if (in_array($this->technology, [
			1, 2, 3, 4
		]))
			$out = 1; // INT
		elseif (in_array($this->technology, [
			5
		]))
			$out = 2; // TEL
		elseif (in_array($this->technology, [
			6, 7, 8, 9
		]))
			$out = 3; // TV

		return $out;
	}

	private function getPackage() {
		$out = null;
		switch ($this->technology) {
			case 1 :
				if ($this->service == 1)
					$out = 1;
				break;
			case 2 :
				if ($this->service == 1)
					$out = 1;
				break;
			case 3 :
				break;
			case 4 :
				break;
			case 5 :
				if ($this->service == 1)
					$out = 1;
				if ($this->service == 1)
					$out = 1;
				break;
			case 6 :
				if ($this->service == 1)
					$out = 1;
				if ($this->service == 1)
					$out = 1;
				if ($this->service == 1)
					$out = 1;
				if ($this->service == 1)
					$out = 1;
				break;
			case 7 :
				if ($this->service == 1)
					$out = 1;
				break;
			case 8 :
				if ($this->service == 1)
					$out = 1;
				break;
			case 8 :
				if ($this->service == 1)
					$out = 1;
				if ($this->service == 1)
					$out = 1;
				break;
		}

		return $out;
	}

	public function execute($queue) {
		$address = $this->getAddress();
		if ($address->save()) {
			$connection = \Yii::createObject([
				'class' => Connection::class, 'soa_id' => $this->soa_id, 'ara_id' => $this->ara_id, 'soa_number' => $this->soa_number,
				'create_at' => $this->create_at, 'start_at' => $this->start_at, 'create_by' => 18, // WTVK user
				'wtvk_create_by' => $this->create_by, 'address_id' => $address->id, 'type_id' => $this->getType(), 'package_id' => $this->getPackage(),
				'phone' => $this->phone, 'phone2' => $this->phone2, 'desc_boa' => $this->desc, 'replaced_id' => $this->replaced_id,
				'phone_at' => $this->phone_at, 'start_at' => $this->start_at, 'mac' => $this->mac
			]);
		} else
			print_r($address->errors);
	}
}

