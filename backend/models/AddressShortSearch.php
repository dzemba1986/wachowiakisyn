<?php

namespace backend\models;

use backend\models\AddressShort;
use yii\data\ActiveDataProvider;

/**
 * AddressSearch represents the model behind the search form about `app\models\AddressShort`.
 */
class AddressShortSearch extends AddressShort
{
	/**
	 * 
	 * {@inheritDoc}
	 * @see \backend\models\AddressShort::rules()
	 */
    public function rules() : array {
    	
        return [
            [['ulica_prefix', 'ulica'], 'safe'],
        ];
    }

    /**
     * 
     * @param array $params
     * @return \yii\data\ActiveDataProvider
     */
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
       			't_gmi',
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
