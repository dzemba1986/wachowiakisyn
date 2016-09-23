<?php

namespace backend\models;

use yii\data\ActiveDataProvider;
use backend\models\Connection;

class ConnectionSearch extends Connection
{	
	// public $socketDate;
	// public $wireDate;
	public $street;
	public $house;
	public $house_detail;
	public $flat;
	public $flat_detail;
	public $minConfDate;
	public $maxConfDate;

    public function rules()
	{
		return $rules = [
			[	
				['ara_id', 'start_date', 'conf_date', 'activ_date', 'pay_date', 'close_date', 'phone_date',
				'add_user', 'conf_user', 'close_user', 'vip', 'nocontract', 'task', 'poll',
				'address', 'phone', 'phone2', 'info', 'info_boa', 'socket', 'again',
				'port', 'device', 'mac', 'type', 'package', 'street', 'house', 'house_detail', 'flat', 'flat_detail',
				'minConfDate', 'maxConfDate'],
				'safe'
			]		
		];
	}
	
	public function search($params)
	{
		$query = Connection::find();
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
            'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],				
		]);
	
		$query->joinWith(['modelAddress', 'modelType']);
		
		$dataProvider->setSort([
			'defaultOrder' => ['start_date' => SORT_ASC, 'street' => SORT_ASC, 'house' => SORT_ASC | SORT_FLAG_CASE, 'flat' => SORT_ASC],
			//'enableMultiSort' => true,	
			'attributes' => [
				'start_date',
				'conf_date',
				'pay_date',
				'close_date',	
				'type',
				'nocontract',
				'poll',
				'socket',
				'again',	
				'task' => [
					'asc' => ['task.start_date' => SORT_ASC],
					'desc' => ['task.start_date' => SORT_DESC]
				],	
				'street' => [
					'asc' => ['ulica' => SORT_ASC],
					'desc' => ['ulica' => SORT_DESC]
				],
				'house' => [
						'asc' => ['dom' => SORT_ASC | SORT_NATURAL | SORT_FLAG_CASE],
						'desc' => ['dom' => SORT_DESC]
				],
				'flat' => [
						'asc' => ['lokal' => SORT_ASC],
						'desc' => ['lokal' => SORT_DESC]
				],
			]
		]);
		
        if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}	

		$query->FilterWhere([
			'connection.id' => $this->id,
			'ara_id' => $this->ara_id,
			'connection.start_date' => $this->start_date,
			'conf_date' => $this->conf_date,
			'activ_date' => $this->address,
			'pay_date' => $this->pay_date,
			'close_date' => $this->close_date,
			'phone_date' => $this->phone_date,
			'connection.type' => $this->type,
			'ulica' => $this->street,
			'dom' => $this->house,
			'lokal' => $this->flat,	
            'nocontract' => $this->nocontract,
			'poll' => $this->poll,
			'socket' => $this->socket,	
            'vip' => $this->vip,
			'again' => $this->again,	
			'task.start_date' => $this->task,	
		]);
	
		$query->andFilterWhere(['like', 'dom_szczegol', $this->house_detail])
			->andFilterWhere(['like', 'lokal_szczegol', $this->flat_detail])
			->andFilterWhere(['>=', 'conf_date', $this->minConfDate])
			->andFilterWhere(['<=', 'conf_date', $this->maxConfDate]);
		
		return $dataProvider;
	}
}
