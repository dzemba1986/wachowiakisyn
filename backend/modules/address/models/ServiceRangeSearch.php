<?php

namespace backend\modules\address\models;

use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ServiceRangeSearch extends ServiceRange {
    
    public function rules() : array {
    	
        return [
            [ArrayHelper::merge(['ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_od'], self::INFRASTRUCTURE_ATTRIBUTES, self::SERVICE_ATTRIBUTES), 'safe'],
        ];
    }

    public function search($params) {
        
        $query = ServiceRange::find()->select(
            ArrayHelper::merge(['id', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal_od', 'lokal_do'], self::INFRASTRUCTURE_ATTRIBUTES, self::SERVICE_ATTRIBUTES)
        );

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 100],
        ]);
        
        $dataProvider->setSort([
            'attributes' => [
                'dom' => [
                    'asc' => ['dom' => new Expression(
                        'case when substring(dom from \'^\d+$\') is null then 9999 else cast(dom as integer) end, dom'
                    )],
                    'desc' => ['dom' => new Expression(
                        'case when substring(dom from \'^\d+$\') is null then 9999 else cast(dom as integer) end DESC, dom DESC'
                    )],
                ],
                'dom_szczegol',
                'lokal_od' => [
                    'asc' => ['lokal_od' => new Expression(
                        'case when substring(lokal_od from \'^\d+$\') is null then 9999 else cast(lokal_od as integer) end, lokal_od'
                    )],
                    'desc' => ['lokal_od' => new Expression(
                        'case when substring(lokal_od from \'^\d+$\') is null then 9999 else cast(lokal_od as integer) end DESC, lokal_od DESC'
                    )]
                ],
            ],
            'defaultOrder' => [
                'dom' => SORT_ASC,
                'dom_szczegol' => SORT_ASC,
                'lokal_od' => SORT_ASC
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ulica_prefix' => $this->ulica_prefix,
        	't_ulica' => $this->ulica,	
            'dom' => $this->dom,
            'lokal_od' => $this->lokal_od,
        ]);

        $query->andFilterWhere(['like', 'dom_szczegol', $this->dom_szczegol]);

        return $dataProvider;
    }
}