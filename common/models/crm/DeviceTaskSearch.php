<?php

namespace common\models\crm;

use yii\data\ActiveDataProvider;

class DeviceTaskSearch extends DeviceTask {
    
    public function rules() {
        
        return [
            [['create_at', 'device_id', 'close_at', 'type_id', 'status', 'subcategory_id', 'category_id', 'create_by', 'close_by', 'fulfit'], 'safe']
        ];
    }
    
    public function search($params) {
        
//         $query = DeviceTask::find()->joinWith(['device', 'addUser', 'closeUser' => function ($q) { $q->from(['closeUser' => User::tableName()]); }]);
        $query = DeviceTask::find()->joinWith(['device', 'closeBy']);

        $dataProvider = new ActiveDataProvider([
			'query' => $query,
            'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],
		]);
	
		$dataProvider->setSort([
		    'defaultOrder' => ['task.status' => SORT_ASC, 'create_at' => SORT_DESC],
			'attributes' => [
				'create_at',
				'close_at',	
				'task.status',
			    'fulfit',
				'type_id',
				'category_id',
				'subcategory_id',
				'create_by',
				'close_by',
				'device_id' => [
					'asc' => ['device.name' => SORT_ASC],
					'desc' => ['device.name' => SORT_DESC],
				],
			]
		]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
        	'device_id' => $this->device_id,
        	'close_by' => $this->close_by,
        	'create_by' => $this->create_by,
            'type_id' => $this->type_id,
            'category_id' => $this->category_id,
            'fulfit' => $this->fulfit,
            'task.status' => $this->status,
            '("task"."create_at")::date' => $this->create_at,
        ]);

        return $dataProvider;
    }
}
