<?php

namespace backend\modules\address\models;

use yii\data\ActiveDataProvider;
use yii\db\Expression;

class AddressSearch extends Address
{
    public function rules() : array {
    	
        return [
            [['ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Address::find()->select(['id', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 100],
        ]);
        
        $dataProvider->setSort([
            'attributes' => [
                'ulica',
                'dom' => [
                    'asc' => ['dom' => new Expression(
                        'case when substring(dom from \'^\d+$\') is null then 9999 else cast(dom as integer) end, dom'
                        )],
                    'desc' => ['dom' => new Expression(
                        'case when substring(dom from \'^\d+$\') is null then 9999 else cast(dom as integer) end DESC, dom DESC'
                        )],
                ],
                'dom_szczegol',
                'lokal' => [
                    'asc' => ['lokal' => new Expression(
                        'case when substring(lokal from \'^\d+$\') is null then 9999 else cast(lokal as integer) end, lokal'
                        )],
                    'desc' => ['lokal' => new Expression(
                        'case when substring(lokal from \'^\d+$\') is null then 9999 else cast(lokal as integer) end DESC, lokal DESC'
                        )]
                ],
                'lokal_szczegol',
                'pietro'
            ],
            'defaultOrder' => [
                'ulica' => SORT_ASC,
                'dom' => SORT_ASC,
                'lokal' => SORT_ASC
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ulica_prefix' => $this->ulica_prefix,
        	't_ulica' => $this->ulica,	
            'dom' => $this->dom,
            'lokal' => $this->lokal,
        ]);

        $query->andFilterWhere(['like', 'dom_szczegol', $this->dom_szczegol])
            ->andFilterWhere(['like', 'lokal_szczegol', $this->lokal_szczegol]);

        return $dataProvider;
    }
}