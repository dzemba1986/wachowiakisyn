<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ModyficationSearch represents the model behind the search form about `app\models\Modyfication`.
 */
class TaskSearch extends Task
{
    public $street;
	public $house;
	public $house_detail;
	public $flat;
	public $flat_detail;
    
    public function rules()
    {
        return [
            [['start_date', 'end', 'color', 'description', 'installer', 'street', 'house', 'house_detail', 
              'flat', 'flat_detail', 'type', 'cost', 'status', 'category', 'add_user', 'close_user'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Task::find();

        $dataProvider = new ActiveDataProvider([
				'query' => $query,
                'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],
				]);
	
		$query->joinWith([
			'modelAddress',
			'modelTaskType',
			'modelTaskCategory',
			'modelAddUser',
        ]);
		
		$dataProvider->setSort([
			'attributes' => [
				'create',
				'start_date',
				'start_time',
				'end_date',
				'end_time',
				'close_date',
				'color',
				'all_day',
				'task.status',
				'type',
				'category',
				'add_user',
				'close_user',	
				'street' => [
						'asc' => ['ulica' => SORT_ASC],
						'desc' => ['ulica' => SORT_DESC],
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
            'id' => $this->id,
            'create' => $this->create,
            'task.start_date' => $this->start_date,
            'start_time' => $this->start_time,
            'end_date' => $this->end_date,
            'end_time' => $this->end_time,
        	'close_date' => $this->close_date,
            'all_day' => $this->all_day,
            'ulica' => $this->street,
			'dom' => $this->house,
			'lokal' => $this->flat,
            'add_user' => $this->add_user,
        	'close_user' => $this->close_user,
        	'task.status' => $this->status,	
            'type' => $this->type,
            'category' => $this->category
            
        ]);

        $query->andFilterWhere(['like', 'installer', $this->installer])->
            andFilterWhere(['like', 'dom_szczegol', $this->house_detail])->
			andFilterWhere(['like', 'lokal_szczegol', $this->flat_detail]);    ;

        return $dataProvider;
    }
}
