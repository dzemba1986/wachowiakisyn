<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class AddressShortSearch extends AddressShort
{
    public function rules() : array {
    	
        return [
            [['ulica_prefix', 'ulica'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = AddressShort::find();

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
        	]	
        ]);
        
        $query->andFilterWhere([
            'ulica_prefix' => $this->ulica_prefix,
        	'ulica' => $this->ulica,	
        ]);

        return $dataProvider;
    }
}
