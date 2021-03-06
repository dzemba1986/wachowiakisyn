<?php
namespace common\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model {
    
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules() {
        
        return [
            [['username', 'password'], 'required', 'message' => 'Wartość wymagana'],
            
            ['rememberMe', 'boolean'],

            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Niewłaściwy login lub hasło.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login() {
        
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() {
        
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
    
    public function attributeLabels() {
    	
    	return array(
    		'username' => 'Login',
    		'password' => 'Hasło',
    		'rememberMe' => 'Zapamiętaj'	
    	);
    }
}
