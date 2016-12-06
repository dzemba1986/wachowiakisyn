<?php

use yii\db\Migration;
use backend\models\ConnectionOld;

class m160315_082820_connection_table_insert_data extends Migration
{
    public function up()
    {
       $arUserMap = [
           0 => 1,
           1 => 9,
           2 => 1,
           3 => 8,
           4 => 2,
           5 => 3,
           6 => 10,
           7 => 1,
           8 => 1,
           9 => 11,
           10 => 1,
           11 => 1,
           12 => 12,
           13 => 1,
           14 => 1,
           15 => 19,
           16 => 9,
           17 => 1,
           18 => 1,
           19 => 1,
           20 => 6,
           21 => 4,
           22 => 8,
           23 => 2,
           24 => 18,
           25 => 13,
           26 => 1,
           27 => 14,
           28 => 15,
           29 => 1,
           30 => 16,
           31 => 6,
           32 => 5,
           33 => 17,
           34 => 7,
       ];
       
       $arTypeMap = [
       
           'net' => 1,
           'phone' => 2,
           'tv' => 3,
           'iptv' => 1,
       ];
       
       $pakiet = function ($x) {
                   
           if($x->service == 'net')
               return 3;
           elseif ($x->service == 'phone') {

               if(is_null($x->moved_phone))
                   return 1;
               else 
                   return 2;
           }
           elseif ($x->service == 'tv')
               return 5;

       };
       
       $pay = function ($y){
           			
           			if(isset($y->service_activation))
           				return $y->service_activation;
           			elseif(isset($y->payment_activation))
           				return $y->payment_activation;
           			else
           				return NULL;
           		};
       
//        $again = function ($x) {
			
//        		$ilosc = ConnectionOld::find()->where([
//        			'localization' => $x->localization,
//        			'service' => $x->service	
//        		])->andWhere('is not', 'resignation_date', null)->count();
       		
//        		if ($ilosc > 0)
//        			return true;
//        		else 
//        			return false;
//        };
       
       $connectionsOld = ConnectionOld::find()->orderBy('start_date')->all();
       
       foreach ($connectionsOld as $connectionOld){ 
           
//            var_dump($connectionOld);
//            exit;
//        	echo $connectionOld->service_activation; exit();
           $this->insert('connection', [
               "id" => $connectionOld->id,
               "ara_id" => isset($connectionOld->ara_id) ? $connectionOld->ara_id : NULL,
               "start_date" => $connectionOld->start_date,
               "conf_date" => isset($connectionOld->service_configuration) ? $connectionOld->service_configuration : NULL,
           		"pay_date" => $pay($connectionOld),
               //"activ_date" => isset($connectionOld->service_activation) ? $connectionOld->service_activation : NULL,
               //'pay_date' => isset($connectionOld->payment_activation) ? $connectionOld->payment_activation : NULL,
               'close_date' => isset($connectionOld->resignation_date) ? $connectionOld->resignation_date : NULL,
               'phone_date' => isset($connectionOld->moved_phone) ? $connectionOld->moved_phone : NULL,
               'add_user' => $arUserMap[$connectionOld->add_user],
               'conf_user' => isset($connectionOld->configuration_user) ? $arUserMap[$connectionOld->configuration_user] : NULL,
               'close_user' => isset($connectionOld->resignation_date) ? 1 : NULL,
               'nocontract' => isset($connectionOld->ara_id)   ?    substr($connectionOld->ara_id, FALSE, TRUE) == 'a' ? TRUE : FALSE    :    FALSE,
//           		'poll' => isset($connectionOld->ara_id)   ?    $connectionOld->ara_id == 'a1234' ? TRUE : FALSE    :    FALSE,
           		//'inea' => $inea($connectionOld),
//            		'again' => $again($connectionOld),
               'vip' => FALSE,
               'port' => $connectionOld->port, //będzie nowa baza SEU
               'mac' => (isset($connectionOld->mac) ) ?     $connectionOld->mac == '' ? NULL : str_replace(':', '', $connectionOld->mac)      :     NULL,                   
               'phone' => isset($connectionOld->phone) ? $connectionOld->phone : NULL,
               'phone2' => isset($connectionOld->phone) ? $connectionOld->phone2 : NULL,
               'info' => isset($connectionOld->info) ? $connectionOld->info : NULL,
               'info_boa' => isset($connectionOld->info_boa) ? $connectionOld->info_boa : NULL,
//                'device' => isset($connectionOld->switch) ? $connectionOld->switch : null,   //będzie nowa baza SEU
               'wire' => 0,
               'socket' => 0,
               'task' => isset($connectionOld->modyfication) && $connectionOld->modyfication <> 0	?	$connectionOld->modyfication	:	null,
               'package' => $pakiet($connectionOld),
               'address' => $connectionOld->localization,
               'type' => $arTypeMap[$connectionOld->service],
           		'synch_date' => is_object($connectionOld->modelBoa) ? $connectionOld->modelBoa->ara_sync : null
           ]);
       }
       
       $this->execute("SELECT setval('connection_id_seq', (SELECT MAX(id) FROM connection))");
    }

    public function down()
    {
        $this->truncateTable('connection');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
