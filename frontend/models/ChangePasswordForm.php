<?php
namespace frontend\models;
 
use common\models\User;
use yii\base\InvalidParamException;
use yii\base\Model;
 
/**
 * Change password form for current user only
 */
class ChangePasswordForm extends Model
{
    public $id;
    public $password;
    public $confirm_password;
 
    private $_user;
 
    public function __construct($id, $config = [])
    {
        $this->_user = User::findIdentity($id);
        
        if (!$this->_user) {
            throw new InvalidParamException('Użytkownik nie został znaleziony!');
        }
        
        $this->id = $this->_user->id;
        parent::__construct($config);
    }
 
    public function rules()
    {
        return [
            [['password','confirm_password'], 'required', 'message' => 'Wartość wymagana'],
            [['password','confirm_password'], 'string', 'min' => 6, 'message' => 'Minimum {min} znaków'],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Pola muszą być identyczne'],
        ];
    }
    
    public function attributeLabels() : array {
        
        return [
            'password' => 'Hasło',
            'confirm_password' => 'Potwierdź hasło',
        ];
    }
 
    public function changePassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
 
        return $user->save(false);
    }
}