<?php

namespace common\models\crm;

use common\models\User;
use yii\data\ActiveDataProvider;

class SelfTaskSearch extends InstallTask {

    public function rules() {
        
        return [
        		[['start_at', 'end_at', 'desc', 'installer', 'street', 'house', 'house_detail', 'minClose', 'maxClose',
              'flat', 'flat_detail', 'cost', 'status', 'category_id', 'create_by', 'close_by', 'paid_psm', 'fulfit'], 'safe']
        ];
    }
    
    public function search($params) {
        
        $query = InstallTask::find()->joinWith(['address', 'createBy', 'closeBy' => function ($q) { $q->from(['closeBy' => User::tableName()]); }]);

        $dataProvider = new ActiveDataProvider([
			'query' => $query,
            'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],
		]);
	
		$dataProvider->setSort([
		    'defaultOrder' => ['start_at' => SORT_ASC],
			'attributes' => [
				'start_at',
				'end_at',
				'close_at',
				'status',
				'type_id',
				'category_id',
				'create_by',
				'close_by',
			    'paid_psm',
			    'fulfit',
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
				]
			]
		]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'end_at' => $this->end_at,
            'close_at' => $this->close_at,
            't_ulica' => $this->street,
			'dom' => $this->house,
        	'dom_szczegol' => $this->house_detail,
			'lokal' => $this->flat,
        	'create_by' => $this->create_by,
        	'close_by' => $this->close_by,
            'paid_psm' => $this->paid_psm,
        	'task.status' => $this->status,	
            'task.type_id' => $this->type_id,
            'category_id' => $this->category_id,
            'fulfit' => $this->fulfit,
        ]);

        $query->andFilterWhere(['like', '("start_at")::text', $this->create_at . '%', false]);
        
//             if (!empty($this->maxClose)) $query->andFilterWhere(['<=', 'close', $this->maxClose . ' 23:59:59']);

        return $dataProvider;
    }
}
