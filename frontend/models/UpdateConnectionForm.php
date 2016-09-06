<?php
namespace frontend\models;

use backend\models\Connection;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class UpdateConnectionForm extends Model
{
    public $ara_id;
    public $phone;
    public $phone2;
    public $mac;
    public $port;
    public $conf_date;
    public $activ_date;
    public $pay_date;
    public $resignation_date;
    public $info;
    public $info_boa;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['ara_id', 'unique', 'targetClass' => '\backend\models\Connection', 'message' => 'Te ara id jest już zajęte'],
            ['ara_id', 'string', 'min' => 5, 'max' => 6],
            
            ['phone', 'string', 'min'=>9, 'max'=>14, 'tooShort'=>'Minimalna długość znaków to 14', 'tooLong'=>'Maksymalna długość znaków to 14', 'skipOnEmpty'=>true],
            
            ['phone2', 'string', 'min'=>9, 'max'=>14, 'tooShort'=>'Minimalna długość znaków to 14', 'tooLong'=>'Maksymalna długość znaków to 14', 'skipOnEmpty'=>true],
            
            ['mac', 'string', 'min'=>17, 'max'=>17, 'tooShort'=>'Minimalna długość znaków to 17', 'tooLong'=>'Maksymalna długość znaków to 17'],
            ['mac', 'unigue', 'targetClass' => '\backend\models\Connection', 'message' => 'Ten adres mac jest już zajęty'],
            ['mac', 'filter', 'filter' => 'trim'],
            
            ['port', 'intiger', 'min' => 1, 'max' => 2],
                      
            ['conf_date', 'date', 'format'=>'yyyy-MM-dd'],
            ['conf_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format daty'],
            ['conf_date', 'default', 'value'=>NULL],
            
            ['pay_date', 'date', 'format'=>'yyyy-MM-dd'],
            ['pay_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format daty'],
            ['pay_date', 'default', 'value'=>NULL],
            
            ['activ_date', 'date', 'format'=>'yyyy-MM-dd'],
            ['activ_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format daty'],
            ['activ_date', 'default', 'value'=>NULL],
            
            ['resignation_date', 'date', 'format'=>'yyyy-MM-dd'],
            ['resignation_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format daty'],
            ['resignation_date', 'default', 'value'=>NULL],
        ];
    }
    
    public function update() {
        
        if ($this->validate()) {
            $modelConnection = new Connection();
            $modelConnection->ara_id = $this->ara_id;
            $modelConnection->phone = $this->phone;
            $modelConnection->phone2 = $this->phone2;
            //$modelConnection->mac = $this->mac; //z ff::ff.... przerobić na dziesiętny
            $modelConnection->port = $this->port;
            
            $modelConnection->conf_date = $this->conf_date;
            $modelConnection->activ_date = $this->activ_date;
            $modelConnection->pay_date = $this->pay_date;
            $modelConnection->resignation_date = $this->resignation_date;
            
            $modelConnection->info = $this->info;
            $modelConnection->info_boa = $this->info_boa;
            
            
            if ($modelConnection->save()) {
                return $modelConnection;
            }
        }

        return null;
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
//    public function signup()
//    {
//        if ($this->validate()) {
//            $user = new User();
//            $user->username = $this->username;
//            $user->first_name = $this->first_name;
//            $user->last_name = $this->last_name;
//            $user->email = $this->email;
//            $user->setPassword($this->password);
//            $user->generateAuthKey();
//            if ($user->save()) {
//                return $user;
//            }
//        }
//
//        return null;
//    }
}
