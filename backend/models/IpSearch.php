<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

/**
 * IpSearch represents the model behind the search form about `app\models\Ip`.
 */
class IpSearch extends Ip
{
	/**
	 * 
	 * {@inheritDoc}
	 * @see \backend\models\Ip::rules()
	 */
    public function rules() : array {
    	
        return [
            [['ip', 'subnet', 'main', 'device'], 'safe'],
        ];
    }

    /**
     * 
     * @param array $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params)
    {
        $query = Ip::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 100],
        ]);
        
        $query->joinWith(['modelDevice']);
        
        $dataProvider->setSort([
        	'attributes' => [
        		'ip',
        		'subnet',
        		'main',
        		'device'	
        	]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->filterWhere([
            'ip' => $this->ip,
        	'subnet' => $this->subnet,
        	'main' => $this->main,
        	'device' => $this->device	
        ]);

        //$query->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }
}
