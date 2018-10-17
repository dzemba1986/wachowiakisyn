<?php

namespace common\models\history;

use yii\data\ActiveDataProvider;

class HistoryIpSearch extends HistoryIp
{	
	public function rules()
	{
		return [
            [['ip', 'to_date'], 'safe'],
        ];
	}
	
	public function search($params)
	{
		$query = HistoryIp::find()->joinWith('address');
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,2000]],
		    'sort' => ['defaultOrder' => ['to_date' => SORT_DESC]]
		]);	
		
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
	
		$query->andFilterWhere([
			'ip' => $this->ip,
		]);
	
		return $dataProvider;
	}
}