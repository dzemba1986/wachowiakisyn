<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

/**
 * ModyficationSearch represents the model behind the search form about `app\models\Modyfication`.
 */
class VlanSearch extends Vlan
{
    public function rules()
    {
        return [
            [['id', 'desc'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Vlan::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 100],
        ]);
        
        $dataProvider->setSort([
        		'defaultOrder' => ['id' => SORT_ASC],
        		'attributes' => [
        				//'id',
        		]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->filterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }
}
