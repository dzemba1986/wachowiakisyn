<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class HistoryIpSearch extends HistoryIp
{	
	public function rules()
	{
		return [
            ['ip', 'safe'],
        ];
	}
	
	public function search($params)
	{
		$query = HistoryIp::find()->joinWith('address');
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,5000]],
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