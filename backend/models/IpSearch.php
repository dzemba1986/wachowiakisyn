<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class IpSearch extends Ip
{
    public function rules() : array {
    	
        return [
            [['ip', 'subnet_id', 'main', 'device_id'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Ip::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 100],
        ]);
        
        $query->joinWith(['device']);
        
        $dataProvider->setSort([
        	'attributes' => [
        		'ip',
        		'subnet_id',
        		'main',
        		'device_id'	
        	]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->filterWhere([
            'ip' => $this->ip,
        	'subnet_id' => $this->subnet_id,
        	'main' => $this->main,
        	'device_id' => $this->device_id	
        ]);

        return $dataProvider;
    }
}