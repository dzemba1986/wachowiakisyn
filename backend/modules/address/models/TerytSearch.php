<?php

namespace backend\modules\address\models;

use yii\data\ActiveDataProvider;

class TerytSearch extends Teryt {
    
    public function rules() : array {
    	
        return [
            [['ulica_prefix', 'ulica'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Teryt::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 100],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $dataProvider->setSort([
       		'attributes' => [
       			'ulica_prefix',	
        		'ulica',
        	],
            'defaultOrder' => [
                'ulica' => SORT_ASC,
            ]
        ]);
        
        $query->andFilterWhere([
            'ulica_prefix' => $this->ulica_prefix,
        	't_ulica' => $this->ulica,	
        ]);

        return $dataProvider;
    }
}
