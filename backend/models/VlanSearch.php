<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

/**
 * VlanSearch represents the model behind the search form about `app\models\Vlan`.
 */
class VlanSearch extends Vlan
{
	/**
	 * 
	 * {@inheritDoc}
	 * @see \backend\models\Vlan::rules()
	 */
    public function rules() : array {
    	
        return [
            [['id', 'desc'], 'safe'],
        ];
    }

    /**
     * 
     * @param array $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params) {
    	
        $query = Vlan::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 100]
        ]);
        
        $dataProvider->setSort([
        	'defaultOrder' => ['id' => SORT_ASC]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->filterWhere([
            'id' => $this->id
        ]);

        $query->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }
}
