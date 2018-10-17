<?php

namespace common\models\crm;

use yii\data\ActiveDataProvider;

class DeviceTaskSearch extends DeviceTask
{
	public $createAtTime;
	public $createAtDate;
	
    public function rules()
    {
        return [
            [['create', 'device_id', 'close', 'type_id', 'status', 'device_type', 'category_id', 'add_user', 'close_user'], 'safe']
        ];
    }
    
    public function search($params)
    {
        $query = DeviceTask::find();

        $dataProvider = new ActiveDataProvider([
			'query' => $query,
            'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],
		]);
	
		$query->joinWith([
			'device',
// 			'deviceType',	
// 			'type',
// 			'category',
			'addUser',
        ]);
		
		$dataProvider->setSort([
			'attributes' => [
				'create',
				'close',	
				'status',
				'type_id',
				'category_id',
				'add_user',
				'close_user',
				'device_id' => [
					'asc' => ['device.name' => SORT_ASC],
					'desc' => ['device.name' => SORT_DESC],
				],
				'device_type'	
			]
		]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
        	'device_id' => $this->device_id,
        	'device_type' => $this->device_type,
        	'when' => $this->when,	
        	'close_user' => $this->close_user,
        	'add_user' => $this->add_user,
            'type_id' => $this->type_id,
            'category_id' => $this->category_id
        ]);
        
        if ($this->status == 'null')
        	$query->andWhere([self::tableName() . '.status' => null]);
        elseif ($this->status == '')
        	$query->andWhere(['or',[self::tableName() . '.status' => true], [self::tableName() . '.status' => false], [self::tableName() . '.status' => null]]);
        else
        	$query->andWhere([self::tableName() . '.status' => $this->status]);

        $query->andFilterWhere(['like', '("create")::text', $this->create.'%', false]);
        //gdy filtr jest pusty bez if'a zapytanie daje zerowy wynik
        if (!empty($this->close)) $query->andFilterWhere(['like', '("close")::text', $this->close.'%', false]);

        return $dataProvider;
    }
}
