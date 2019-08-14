<?php
namespace common\models\rmq;

use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

class ErrorJob extends Job implements JobInterface {
    
    const TYPE = 3;
    
    public $desc;
    
    public function __construct($caseId, $desc, $config = []) {
        
        $this->case_id = $caseId;
        $this->desc = $desc;
        
        parent::__construct($config);
    }
    
    public function fields() {
        
        return ArrayHelper::merge(
            parent::fields(),
            [
                'desc'
            ]
        );
    }
    
    public function rules() {
        
        return [
            ['desc', 'required', 'message' => 'Wartość wymagana'],
            ['desc', 'string'],
        ];
    }
    
    public function execute($queue) {
        ;
    }
}

