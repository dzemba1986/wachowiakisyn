<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $first_name;
    public $last_name;
    public $email;
    public $password;

    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required', 'message' => 'Login wymagany'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Ten login jest już używany.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
        		
        	['first_name', 'required', 'message' => 'Imię wymagane'],
        	
        	['last_name', 'required', 'message' => 'Nazwisko wymagane'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required', 'message' => 'Email wymagany'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Ten email jest już używany.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }
    
    public function attributeLabels(){
    	
    	return array(
    		'username' => 'Login',
    		'first_name' => 'Imię',
    		'last_name' => 'Nazwisko',	
    		'password' => 'Hasło',
    		'email' => 'e-mail'	
    	);
    }

    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }
}
