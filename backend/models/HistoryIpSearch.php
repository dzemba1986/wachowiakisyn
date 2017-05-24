<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class HistoryIpSearch extends HistoryIp
{	
	public function rules()
	{
		return [
		// The following rule is used by search().
		// @todo Please remove those attributes that should not be searched.
		[
            ['ip'], 'safe'],
		];
	}
	
	public function search($params)
	{
		$query = HistoryIp::find()->joinWith('modelAddress');
	
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