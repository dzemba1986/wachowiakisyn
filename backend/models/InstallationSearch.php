<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class InstallationSearch extends Installation
{	
	public $street;
	public $house;
	public $house_detail;
	public $flat;
	public $flat_detail;
	public $minSocketDate;
	public $maxSocketDate;
    
	public function rules()
	{
		return [
		[['address', 'wire_length', 'type_id', 'status',
		 'wire_date', 'socket_date', 'minSocketDate', 'maxSocketDate',
		 'wire_user', 'socket_user', 'invoice_date', 'street', 'house', 'house_detail', 'flat', 'flat_detail'], 
		 'safe'],
		];
	}
	
	public function search($params)
	{
		$query = Installation::find();
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
            'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,1000]],
		]);
	
		$query->joinWith(['address', 'type']);
		
		$dataProvider->setSort([
			'defaultOrder' => ['socket_date' => SORT_ASC, 'street' => SORT_ASC, 'house' => SORT_ASC, 'flat' => SORT_ASC],
			'attributes' => [
				'wire_length',
				'wire_date',
				'socket_date',
				'wire_user',
				'socket_user',
				'invoice_date',
                'type_id',
                'street' => [
					'asc' => ['t_ulica' => SORT_ASC],
					'desc' => ['t_ulica' => SORT_DESC],
				],	
				'house' => [
						'asc' => ['dom' => SORT_ASC],
						'desc' => ['dom' => SORT_DESC],
				],
				'house_detail' => [
					'asc' => ['dom_szczegol' => SORT_ASC],
					'desc' => ['dom_szczegol' => SORT_DESC],
				],
				'flat' => [
						'asc' => ['lokal' => SORT_ASC],
						'desc' => ['lokal' => SORT_DESC],
				],
			]
		]);
		
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
	
		$query->andFilterWhere([
			'tbl_installation.id' => $this->id,
			'wire_length' => $this->wire_length,
			'wire_date' => $this->wire_date,
			'socket_date' => $this->socket_date,
			'type_id' => $this->type_id,
			't_ulica' => $this->street,
			'dom' => $this->house,
			'lokal' => $this->flat,
			'invoiced' => $this->invoice_date,
			'status' => $this->status
		]);
		
		
	
		$query->andFilterWhere(['like', 'wire_user', $this->wire_user])
			->andFilterWhere(['like', 'socket_user', $this->socket_user])
            ->andFilterWhere(['like', 'dom_szczegol', $this->house_detail])
			->andFilterWhere(['like', 'lokal_szczegol', $this->flat_detail])
			->andFilterWhere(['>=', 'socket_date', $this->minSocketDate])
			->andFilterWhere(['<=', 'socket_date', $this->maxSocketDate]);
	
		return $dataProvider;
	}
}