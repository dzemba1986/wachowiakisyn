<?php
use yii\db\Migration;
use backend\models\AddressOld;

class m160315_082106_address_table_insert_data extends Migration
{
    public function up()
    {
       $arStreetMap = [
           '01957' => 'Bóżnicza',
           '03269' => 'Czarna Rola',
           '09439' => 'Kosmonautów',
           '12272' => 'Marcelińska',
           '13631' => 'Na Murawie',
           '13989' => 'Naramowicka',
           '15776' => 'Pasterska',
           '16636' => 'Pod Lipami',
           '17923' => 'Przyjaźni',
           '19232' => 'Kondratija Rylejewa',
           '22907' => 'Towarowa',
           '23990' => 'Wichrowe Wzgórze',
           '24263' => 'Wilczak',
           '26323' => 'Zwycięstwa',
       		'23306' => 'Ugory',
       ];
       
       	function getPrefix($t_ulic){
			
       		if ($t_ulic == '09439' ||
       			$t_ulic == '13631' ||
       			$t_ulic == '16636' ||
       			$t_ulic == '17923' ||
       			$t_ulic == '23990' ||
       			$t_ulic == '26323')
       				return 'os.';
       		else 
       			return 'ul.';
       			
       	}
       
       $addressesOld = AddressOld::find()->all();
       
       foreach ($addressesOld as $addressOld){ 
       
           //var_dump($addressOld);
           //exit;
           
           $this->insert('address', [
               "id" => $addressOld->id,
               "t_woj" => '30',
               "t_pow" => '64',
               "t_gmi" => '05',
               "t_rodz" => '9',
               't_miasto' => '0970224',
               't_ulica' => $addressOld->ulic,
               'ulica_prefix' => getPrefix($addressOld->ulic),
               'ulica' => $arStreetMap[$addressOld->ulic],
               'dom' => $addressOld->blok,
               'dom_szczegol' => $addressOld->klatka,
               'lokal' => $addressOld->mieszkanie,
               'lokal_szczegol' => $addressOld->nazwa_inna,
           ]);
       }
       
       $this->execute("SELECT setval('address_id_seq', (SELECT MAX(id) FROM address))");
    }
    

    public function down()
    {
        $this->truncateTable('address');
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
