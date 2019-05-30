<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var string $address
 * @var string $type
 * @var string $model
 * @var string $manufacturer
 * @var array $vlansToIps[]
 * @var backend\models\OpticalAmplifier $device
 */

echo Html::beginTag('div', ['class' => 'col-md-5']);
echo DetailView::widget([
	'model' => $device,
	'attributes' => [
		'id',	
		[
			'label' => 'Adres',
			'value' => $device->addressName
		],
		[
			'label' => 'Status',
			'value' => $device->status ? 'Aktywny' : 'Nieaktywny'
		],
		[
			'label' => 'Typ',
			'value' => $device->typeName
		],
	    [
	        'label' => 'Ip',
	        'value' => Html::a("{$device->vlansToIps[0]['ip']} [ vlan{$device->vlansToIps[0]['vlan_id']} ]", "http://{$device->vlansToIps[0]['ip']}", ['target'=>'_blank']),
	        'format' => 'raw',
        ],
		[
			'label' => 'Mac',
			'value' => $device->mac,
		],
		'serial',
		[
			'label' => 'Model',
			'value' => $device->modelName,
		],
		[
			'label' => 'Producent',
			'value' => $device->manufacturerName,
		],
		'desc',
	]
]);
echo Html::endTag('div');
?>