<?php

namespace common\models\seu\network;

use yii\data\ActiveDataProvider;

class SubnetSearch extends Subnet
{
    public function rules()
    {
        return [
            [['ip', 'desc', 'dhcp'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Subnet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 100],
        ]);
        
        $dataProvider->setSort([
        		'attributes' => [
        			'ip',
        			'dhcp'	
        		]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->filterWhere([
            'ip' => $this->ip,
        	'dhcp' => $this->dhcp	
        ]);

        $query->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }
}
