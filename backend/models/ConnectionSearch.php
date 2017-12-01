<?php

namespace backend\models;

use yii\data\ActiveDataProvider;
use backend\models\Connection;
use yii\db\Expression;
use backend\modules\task\models\InstallTask;

class ConnectionSearch extends Connection
{	
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
				['ara_id', 'start_date', 'conf_date', 'pay_date', 'close_date', 'phone_date', 'synch_date',
				'add_user', 'conf_user', 'close_user', 'nocontract', 'task_id',
				'socket', 'again', 'wire',
				'type', 'package', 'street', 'house', 'house_detail', 'flat', 'flat_detail',
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
	
		$query->joinWith(['modelAddress', 'modelType', 'modelPackage']);
		
		$dataProvider->setSort([
			'defaultOrder' => [
				'start_date' => SORT_ASC, 
				'street' => SORT_ASC, 
				'house' => new Expression('case when substring(dom from \'^\d+$\') is null then 9999 else cast(dom as integer) end, dom'), 
				'flat' => new Expression('case when substring(lokal from \'^\d+$\') is null then 9999 else cast(lokal as integer) end, lokal')
			],
			//'enableMultiSort' => true,	
			'attributes' => [
				'start_date',
				'conf_date',
				'pay_date',
				'close_date',
				'synch_date',	
				'type',
				'package',	
				'nocontract',
				'socket',
				'again',	
				'task_id' => [
					'asc' => ['task.start' => SORT_ASC],
					'desc' => ['task.start' => SORT_DESC]
				],	
				'street' => [
					'asc' => ['ulica' => SORT_ASC],
					'desc' => ['ulica' => SORT_DESC]
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
			'start_date' => $this->start_date,
			'conf_date' => $this->conf_date,
			'pay_date' => $this->pay_date,
			'close_date' => $this->close_date,
			'phone_date' => $this->phone_date,
			Connection::tableName().'.type' => $this->type,
			'package' => $this->package,
			'ulica' => $this->street,
			'dom' => $this->house,
			'lokal' => $this->flat,	
            Connection::tableName().'.nocontract' => $this->nocontract,
			'wire' => $this->wire,
			'again' => $this->again,	
			InstallTask::tableName().'.start' => $this->task_id,	
		]);
		
		if (isset($params['ConnectionSearch']['socket']) && $params['ConnectionSearch']['socket'] == '1'){
			$query->andFilterWhere(['>', 'socket', 0]);
		} elseif(isset($params['ConnectionSearch']['socket']) && $params['ConnectionSearch']['socket'] == '0')
			$query->andFilterWhere(['socket' => 0]);
	
		$query->andFilterWhere(['like', 'dom_szczegol', $this->house_detail])
			->andFilterWhere(['like', 'lokal_szczegol', $this->flat_detail])
			->andFilterWhere(['like', '{{synch_date}}::text', $this->synch_date.'%', false]);

		
		return $dataProvider;
	}
}
