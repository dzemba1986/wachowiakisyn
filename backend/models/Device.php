<?php

namespace backend\models;

use yii\db\Query;
use yii\db\ActiveRecord;
/**
 * @property integer $id
 * @property boolean $status
 * @property string $name
 * @property string $proper_name
 * @property string $desc
 * @property integer $address_id
 * @property integer $type_id
 */

class Device extends ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_DELETE = 'delete';
	const SCENARIO_TOSTORE = 'toStore';
	const SCENARIO_TOTREE = 'toTree';
	
	public static function tableName()
	{
		return '{{device}}';
	}
	
	public function attributes(){
	    
	    return [
	        'id',
	        'status',
	        'name',
	        'desc',
	        'address_id',
	        'type_id'
	    ];
	}
	
	public static function instantiate($row)
	{
		switch ($row['type']) {
			case Host::TYPE:
				return new Host() ;
			case Swith::TYPE:
				return new Swith() ;
			case Router::TYPE:
				return new Router();
			case Camera::TYPE:
				return new Camera();
			case GatewayVoip::TYPE:
				return new GatewayVoip();
			case MediaConverter::TYPE:
				return new MediaConverter();
			case Server::TYPE:
				return new Server();
			case Virtual::TYPE:
				return new Virtual();
			case Root::TYPE:
				return new Root();
			case Server::TYPE:
				return new Server();
			default:
				return new self;
		}
	}
	
	public function rules(){
		
		return [
            
            ['status', 'boolean'],
			['status', 'default', 'value' => null],
            ['status', 'required', 'message' => '{attribute} jest wymagany', 'on' => [self::SCENARIO_TOSTORE, self::SCENARIO_TOTREE]],
                      
            ['name', 'string', 'min' => 3, 'max' => 20],
				
            ['desc', 'string'],
            
            ['address_id', 'integer'],
            ['address_id', 'required', 'on' => self::SCENARIO_TOTREE],
            
            ['type_id', 'integer'],
            ['type_id', 'required', 'message' => 'Wartość wymagana'],           
				
			[['status', 'desc', 'address_id', 'type_id', 'name'], 'safe'],
		];
	}
    
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['desc', 'address', 'type'];
		$scenarios[self::SCENARIO_UPDATE] = ['status', 'name', 'desc', 'original_name'];
		$scenarios[self::SCENARIO_TOSTORE] = ['address', 'status'];
		$scenarios[self::SCENARIO_TOTREE] = ['address', 'status'];
			
		return $scenarios;
	}
	
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            if(!empty($this->serial))   
                $this->serial = strtoupper($this->serial);
            
            if(!empty($this->mac))
              	$this->mac = strtolower($this->mac);

            return true;
        }
    }

		/**
	 * @return array relational rules.
	 */
	public function getModelAddress(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(Address::className(), ['id'=>'address']);
	}
    
    public function getModelIps(){
	
		//urządzenie ma wiele aresów IP
		return $this->hasMany(Ip::className(), ['device'=>'id'])->orderBy(['main' => SORT_DESC]);
	}
        
    public function getModelType(){
	
		//urządzenie ma jeden typ
		return $this->hasOne(DeviceType::className(), ['id'=>'type']);
	}
    
    public function getModelModel(){
	
		//urządzenie ma jeden typ
		return $this->hasOne(Model::className(), ['id'=>'model']);
	}
    
    public function getModelManufacturer(){
	
		//urządzenie ma jeden typ
		return $this->hasOne(Manufacturer::className(), ['id'=>'manufacturer']);
	}
    
    public function getModelTree(){
	
		//urządzenie ma jeden typ
		return $this->hasMany(Tree::className(), ['device'=>'id']);
	}
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
    
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => 'Status',
            'name' => 'Nazwa',
			'original_name' => 'Org.',	
            'desc' => 'Opis',
			'address' => 'Adres',
			'type' => 'Typ',
			'manufacturer' => 'Producent'	
		);
	}
	
	public static  function getDeviceList(){
		
		$query = new Query();
		$query->select(['d.id', 'a.t_ulica', new \yii\db\Expression("
	    		CASE
	    			WHEN pietro IS NULL THEN
	    				CONCAT(adrs.name, ' ', dom, dom_szczegol, ' ', '[', ip, ']')
	    			ELSE
	    				CONCAT(adrs.name, ' ', dom, dom_szczegol, ' (piętro', pietro, ')', ' ', '[', ip, ']')
	    		END
	    	")])
    	->from('device d')
    	->join('INNER JOIN', 'address a', 'a.id = d.address')
    	->join('INNER JOIN', 'ip', 'ip.device = d.id')
    	->join('INNER JOIN', 'address_short adrs', 'adrs.t_ulica = a.t_ulica')
    	->where(['status' => true]);
    	$command = $query->createCommand();
    	
    	return $command->queryAll();
	}
}
