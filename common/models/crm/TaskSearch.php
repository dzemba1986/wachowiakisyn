<?php

namespace common\models\crm;

use yii\data\ActiveDataProvider;

class TaskSearch extends Task {
    
    public function rules() {
        
        return [
            [['create_at', 'status', 'category_id', 'subcategory_id', 'type_id', 'create_by', 'close_by', 'close_at', 'address_id', 'programme'], 'safe']
        ];
    }
    
    public function search($params) {
        
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
			'query' => $query,
            'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],
		]);
	
		$query->joinWith([
		    'createBy', 
		    'address', 
		    'type', 
		    'category' => function ($q) { $q->from(['category' => TaskCategory::tableName()]); },
		    'subcategory' => function ($q) { $q->from(['subcategory' => TaskCategory::tableName()]); },
        ]);
		
		$dataProvider->setSort([
			'attributes' => [
				'create_at',
				'status',
				'type_id',
				'category_id',
				'subcategory_id',
				'create_by',
			]
		]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
        	'task.status' => $this->status,	
            'type_id' => $this->type_id,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'programme' => $this->programme,
            '("task"."create_at")::date' => $this->create_at,
        ]);
        
        return $dataProvider;
    }
}
