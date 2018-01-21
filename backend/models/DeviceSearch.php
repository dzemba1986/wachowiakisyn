<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class DeviceSearch extends Device
{	
	public function rules()
	{
		return [
		[
            ['id', 'status', 'address_id', 'mac', 'name', 'type_id', 'desc', 'model_id', 'manufacturer_id', 'serial'],
            'safe'],
		];
	}
	
	public function search($params)
	{
		$query = Device::find();
	
		$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],
				]);	
		
		$dataProvider->setSort([
			'attributes' => [
				'status',
				'name',
				'mac',
				'address_id',
				'type_id',
                'serial',
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
				'address_id' => $this->address_id,
                'type_id' => $this->type_id,
                'manufacturer_id' => $this->manufacturer_id,
                'model_id' => $this->model_id,
				]);
		
		
	
		$query->andFilterWhere(['like', 'desc', $this->desc])
			->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', new \yii\db\Expression('CAST(mac AS varchar)') , $this->mac]);    
	
		return $dataProvider;
	}
}