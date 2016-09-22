<?php 

use yii\db\Migration;
use backend\models\TaskOld;

//skrypt inportujący dane z starych tabel
//
//  *****************Modyfication*****************************************
//  
// 1. Usunąć rekordy z wartościami "0000-00-00 00:00:00" w polach z datę
// 2. Usunąć rekordy z wartościami NULL w kolumnach "mod_inst" i "mod_type"
//
//  *****************Installation*****************************************
//  
// 1. Dodać wartość tv w kolumnach "type" - jest umowa o ip tv bez typu

class m160315_082942_task_table_insert_data extends Migration
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
       
       $tasksOld = TaskOld::find()->all();
       
       $arTypeTaskMap = [
           'net' => 1,
           'phone' => 2,
           'tv' => 3,
           'other' => 4,
           'net_phone' => 4,
           'bu_net' => 1,
           'bu_phone' => 2,
           'bu_net_phone' => 4,
       ];
       
       $arCategoryTaskMap = [
           'inst_new' => 1,
           'inst_change' => 1,
           'socket_add' => 3,
           'socket_change' => 3,
           'socket_move' => 3,
           'wire_change' => 2,
           'modyfication' => 4
       ];
       
       foreach ($tasksOld as $taskOld){ 
       
           
           if(isset($taskOld->mod_s_datetime)){
               $sdate = new DateTime($taskOld->mod_s_datetime);
           }
           else
               $sdate = NULL;
           
           if(isset($taskOld->mod_e_datetime)){
               $edate = new DateTime($taskOld->mod_e_datetime);
           }
           else
               $edate = NULL;
           
//            var_dump($taskOld);
//            exit();
           
           $this->insert('task', [
               "id" => $taskOld->mod_id,
               "create" => isset($taskOld->mod_a_datetime) ? $taskOld->mod_a_datetime : NULL,
               "start_date" => isset($sdate) ? $sdate->format('Y-m-d') : NULL,
               "start_time" => isset($sdate) ? $sdate->format('H:i:s') : NULL,
               "end_date" => isset($edate) ? $edate->format('Y-m-d') : NULL,
               "end_time" => isset($edate) ? $edate->format('H:i:s') : NULL,
			   'close_date' =>  isset($taskOld->mod_close_datetime) ? $taskOld->mod_close_datetime : null,
               'color' => NULL, //kolorystyka w zależności od typu montażu
               'class_name' => NULL, //czy będzie wykorzystywane? 
               'all_day' => FALSE,
               'editable' => $taskOld->mod_a_datetime < date('Y-m-d H:i:s') ? FALSE : TRUE,
               'status' => isset($taskOld->mod_close_datetime)   ?   $taskOld->mod_fullfill == TRUE ? TRUE : FALSE       :   TRUE,
               'cost' => isset($taskOld->mod_cost) ? $taskOld->mod_cost : 0,
               'installer' => isset($taskOld->mod_installer) ? $taskOld->mod_installer : NULL,
               'phone' => isset($taskOld->modelConnection) ? $taskOld->modelConnection->phone : NULL, //ściągnąć z tabeli conn... jeżeli jest powiązanie
               'description' => isset($taskOld->mod_desc) ? $taskOld->mod_desc : NULL,
               'address' => isset($taskOld->mod_loc) ? $taskOld->mod_loc : NULL,
               'category' => isset($taskOld->mod_type) ? $arCategoryTaskMap[$taskOld->mod_type] : NULL,
               'type' => isset($taskOld->mod_inst) ? $arTypeTaskMap[$taskOld->mod_inst] : NULL,
               'add_user' => isset($taskOld->mod_user_add) ? $arUserMap[$taskOld->mod_user_add] : NULL,
               'close_user' => isset($taskOld->mod_user_closed) ? $arUserMap[$taskOld->mod_user_closed] : NULL,
           ]);
       }
       
       $this->execute("SELECT setval('task_id_seq', (SELECT MAX(id) FROM task))");
    }

    public function down()
    {
        $this->truncateTable('task');
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
