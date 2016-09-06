<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class DeviceSearch extends Device
{	
	public function rules()
	{
		return [
		// The following rule is used by search().
		// @todo Please remove those attributes that should not be searched.
		[
            ['id', 'status', 'address', 'mac', 'name', 'type', 'desc', 'model', 'manufacturer', 'serial'],
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
				'address',
				'type',
                'serial',
                'model',
                'manufacturer',
			]
		]);
		
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
	
		$query->andFilterWhere([
				'id' => $this->id,
				'status' => $this->status,
				//'mac' => $this->mac,
				'address' => $this->address,
                'type' => $this->type,
                'manufacturer' => $this->manufacturer,
                'model' => $this->model,
				]);
		
		
	
		$query->andFilterWhere(['like', 'desc', $this->desc])
			->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', new \yii\db\Expression('CAST(mac AS varchar)') , $this->mac]);    
	
		return $dataProvider;
	}
}