<?php

use yii\widgets\DetailView;


echo DetailView::widget([
		'model' => $modelDevice,
		'options' => [
				'class' => 'table table-striped table-bordered detail-view',
		],
		'attributes' => [
			[
				'label' => 'Adres',
				'value' => $modelDevice->modelAddress->fullDeviceAddress
			],
			[
				'label' => 'Status',
				'value' => $modelDevice->status ? 'Aktywny' : 'Nieaktywny'
			],
			[
				'label' => 'Typ',
				'value' => $modelDevice->modelType->name
			],
			[
				'label' => 'Mac',
				'value' => $modelDevice->mac,
			],
			'serial',
			[
				'label' => 'Model',
				'value' => $modelDevice->modelModel->name,
			],
			[
				'label' => 'Producent',
				'value' => $modelDevice->modelManufacturer->name,
			],
			[
				'label' => 'Adresacja',
				'value' => $modelDevice->modelIp[0]->ip,
			],
		]
]);