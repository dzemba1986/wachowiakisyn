<?php

namespace common\models\soa;

use yii\data\ActiveDataProvider;
use yii\db\Expression;

class ConnectionSearch extends Connection {
    
	public $street;
	public $house;
	public $house_detail;
	public $flat;
	public $flat_detail;
	public $minConfDate;
	public $maxConfDate;

    public function rules() {
        
		return [
			[['type_id', 'package_id', 'start_at', 'street'], 'safe']		
		];
	}
	
	public function search($params) {
	    
		$query = self::find();
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
            'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],				
		]);
	
		$query->joinWith(['address', 'type', 'package' => function ($q) { $q->from(['package' => Package::tableName()]); }]);
		
		$dataProvider->setSort([
			'defaultOrder' => [
				'start_at' => SORT_ASC, 
				'street' => SORT_ASC, 
				'house' => new Expression('case when substring(dom from \'^\d+$\') is null then 9999 else cast(dom as integer) end, dom'), 
				'flat' => new Expression('case when substring(lokal from \'^\d+$\') is null then 9999 else cast(lokal as integer) end, lokal')
			],
			'attributes' => [
				'start_at',
				'conf_at',
				'pay_at',
				'close_at',
				'synch_at',	
				'type_id',
				'package_id',	
				'nocontract',
				'socket',
				'again',	
				'task_id' => [
					'asc' => ['task.start' => SORT_ASC],
					'desc' => ['task.start' => SORT_DESC]
				],	
				'street' => [
					'asc' => ['t_ulica' => SORT_ASC],
					'desc' => ['t_ulica' => SORT_DESC]
				],
				'house' => [
						'asc' => ['dom' => new Expression(
							'case when substring(dom from \'^\d+$\') is null then 9999 else cast(dom as integer) end, dom'
						)],
						'desc' => ['dom' => new Expression(
							'case when substring(dom from \'^\d+$\') is null then 9999 else cast(dom as integer) end DESC, dom DESC'
						)],
				],
				'flat' => [
						'asc' => ['lokal' => new Expression(
							'case when substring(lokal from \'^\d+$\') is null then 9999 else cast(lokal as integer) end, lokal'
						)],
						'desc' => ['lokal' => new Expression(
							'case when substring(lokal from \'^\d+$\') is null then 9999 else cast(lokal as integer) end DESC, lokal DESC'
						)]
				],
			]
		]);
		
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
			
		$query->FilterWhere([
			'ara_id' => $this->ara_id,
			'soa_id' => $this->soa_id,	
			'"date"(start_at)' => $this->start_at,
			'conf_at' => $this->conf_at,
			'pay_at' => $this->pay_at,
		    '"date"(synch_at)' => $this->synch_at,
			'"date"(close_at)' => $this->close_at,
			'move_phone_at' => $this->move_phone_at,
			'connection.type_id' => $this->type_id,
			'package_id' => $this->package_id,
			't_ulica' => $this->street,
			'dom' => $this->house,
		    'upper(dom_szczegol)' => strtoupper($this->house_detail),
			'lokal' => $this->flat,	
            'connection.nocontract' => $this->nocontract,
			//'wire' => $this->wire,
			'again' => $this->again,
		    'vip' => $this->vip,
			'"date"(task.start)' => $this->task_id,	
		]);
		
		if (isset($params['ConnectionSearch']['wire'])) {
		    if ($params['ConnectionSearch']['wire'] == '1') $query->andFilterWhere(['>', 'wire', 0]);
            elseif ($params['ConnectionSearch']['wire'] == '0') $query->andFilterWhere(['wire' => 0]);
		}
		
		if (isset($params['ConnectionSearch']['socket'])) {
		    if ($params['ConnectionSearch']['socket'] == '1') $query->andFilterWhere(['>', 'socket', 0]);
		    elseif ($params['ConnectionSearch']['socket'] == '0') $query->andFilterWhere(['socket' => 0]);
		}
		
		$query->andFilterWhere(['like', 'lower(lokal_szczegol)', strtolower($this->flat_detail)]);

		return $dataProvider;
	}
}
