<?php

namespace common\models\crm;

use yii\data\ActiveDataProvider;

class InstallTaskSearch extends InstallTask
{
    public function rules()
    {
        return [
        		[['start', 'end', 'color', 'description', 'installer', 'street', 'house', 'house_detail', 'minClose', 'maxClose',
              'flat', 'flat_detail', 'type_id', 'cost', 'status', 'category_id', 'add_user', 'close_user', 'paid_psm'], 'safe']
        ];
    }
    
    public function search($params)
    {
        $query = InstallTask::find();

        $dataProvider = new ActiveDataProvider([
			'query' => $query,
            'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],
		]);
	
		$query->joinWith([
			'address',
			'type',
			'category',
			'addUser',
        ]);
		
		$dataProvider->setSort([
			'attributes' => [
				'create',
				'start',
				'end',
				'close',
				'color',
				'status',
				'type_id',
				'category_id',
				'add_user',
				'close_user',
			    'paid_psm',
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
            'id' => $this->id,
            'end' => $this->end,
            'close' => $this->close,
            't_ulica' => $this->street,
			'dom' => $this->house,
        	'dom_szczegol' => $this->house_detail,
			'lokal' => $this->flat,
        	'add_user' => $this->add_user,
        	'close_user' => $this->close_user,
            'paid_psm' => $this->paid_psm,
        	'task.status' => $this->status,	
            'task.type_id' => $this->type_id,
            'category_id' => $this->category_id
        ]);

        $query->andFilterWhere(['like', 'installer', $this->installer])
        	->andFilterWhere(['like', '("start")::text', $this->start.'%', false])
        	->andFilterWhere(['like', 'lokal_szczegol', $this->flat_detail])
        	->andFilterWhere(['>=', 'close', $this->minClose]);
        
        if (!empty($this->maxClose)) $query->andFilterWhere(['<=', 'close', $this->maxClose . ' 23:59:59']);

        return $dataProvider;
    }
}
