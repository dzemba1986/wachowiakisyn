<?php

namespace common\models\seu\devices;

use yii\data\ActiveDataProvider;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;

class DeviceSearch extends BusinessDevice {
    
	public function rules() {
	    
	    return [
            [['status', 'address_id', 'mac', 'name', 'type_id', 'desc', 'model_id', 'manufacturer_id', 'serial'], 'safe']
        ];
	}
	
	public function search($params) {
	    
		$query = self::find();
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,2000]],
		]);
		
		$query->joinWith(['manufacturer', 'model', 'type']);
		
		$dataProvider->setSort([
			'attributes' => [
				'status',
				'mac',
			    'serial',
				'device.type_id',
                'model_id',
                'device.manufacturer_id',
			]
		]);
		
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
		
		$query->andFilterWhere([
			'id' => $this->id,
			'status' => $this->status,
		    'device.type_id' => $this->type_id,
			'address_id' => $this->address_id,
            'device.manufacturer_id' => $this->manufacturer_id,
            'model_id' => $this->model_id,
		]);
	
		$query->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', '"mac"::text' , MacaddressValidator::formatValue($this->mac)]);    
	
		return $dataProvider;
	}
}