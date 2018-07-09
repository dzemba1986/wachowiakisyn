<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class DeviceSearch extends Device
{	
	public function rules()
	{
		return [
		[['status', 'address_id', 'mac', 'name', 'type_id', 'desc', 'model_id', 'manufacturer_id', 'serial'], 'safe'],];
	}
	
	public function search($params)
	{
		$query = Device::find();
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,2000]],
		]);	
		
		$dataProvider->setSort([
			'attributes' => [
				'status',
				'name',
				'mac',
			    'serial',
				'address_id',
				'type_id',
                'model_id',
                'manufacturer_id',
			]
		]);
		
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
	
		$query->andFilterWhere([
			'id' => $this->id,
			'status' => $this->status,
		    'type_id' => $this->type_id,
			'address_id' => $this->address_id,
            'manufacturer_id' => $this->manufacturer_id,
            'model_id' => $this->model_id,
		]);
	
		$query->andFilterWhere(['like', 'desc', $this->desc])
			->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', '"mac"::text' , preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1:$2:$3:$4:$5:$6', str_replace([':', '.', '-'], '', $this->mac))]);    
	
		return $dataProvider;
	}
}