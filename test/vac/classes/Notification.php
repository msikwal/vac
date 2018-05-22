<?php
require("../includes/Db.class.php");
class Notification 
{
	private $db;
	public function __construct($db)
	{ 			
		$this->db = $db;	
	}

	public function getPregNotificationDet(){
		$result = $this->db->query("Select a.id,a.user_id,d.mother_name as name,a.msg_id,a.msg_type,'a' as vac_name, a.doc_id,a.start_date,b.salutation, b.first_name, b.last_name, b.mobile, b.refer_by_doc, b.status,'Appointment with' as text_msg FROM msg_schedule_master a , user_details b , doc_details c ,pregenancy_details d where a.user_id = b.user_id and a.doc_id = c.mobile and a.msg_id = d.id and a.user_id = d.user_id and a.msg_send_status != 'send' and a.msg_type = 'pregnancy' 
AND UNIX_TIMESTAMP (a.start_date) >= UNIX_TIMESTAMP(CURDATE()) 
AND UNIX_TIMESTAMP (a.start_date) < UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL +3 DAY))
ORDER BY a.user_id");
		return $result;
	}

	public function getHealthNotificationDet(){
		$result = $this->db->query("Select a.id,a.user_id,d.patient_name as name,a.msg_id,a.msg_type,'a' as vac_name, a.doc_id,a.start_date,b.salutation, b.first_name, b.last_name, b.mobile, b.refer_by_doc, b.status,'Appointment with' as text_msg FROM msg_schedule_master a , user_details b , doc_details c ,patient_details d where a.user_id = b.user_id and a.doc_id = c.mobile and a.msg_id = d.id and a.user_id = d.user_id and a.msg_send_status != 'send' and a.msg_type = 'health' 
AND UNIX_TIMESTAMP (a.start_date) >= UNIX_TIMESTAMP(CURDATE()) 
AND UNIX_TIMESTAMP (a.start_date) < UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL +3 DAY))
ORDER BY a.user_id");
		return $result;
	}
	
	public function getNotificationDet(){
		$result = $this->db->query("Select a.id,a.user_id,a.msg_id,a.msg_type, a.doc_id,a.start_date,d.vac_name,b.salutation,e.child_name as name, b.first_name, b.last_name, b.mobile, b.refer_by_doc, b.status,c.text_msg FROM msg_schedule_master a ,user_details b ,doc_details c ,vac_details d,child_details e where a.user_id = b.user_id and a.doc_id = c.mobile and a.vac_id = d.vac_id and a.msg_id = e.child_id and a.user_id = e.parent_id and a.msg_send_status != 'send' AND UNIX_TIMESTAMP (a.start_date) >= UNIX_TIMESTAMP(CURDATE()) AND UNIX_TIMESTAMP (a.start_date) < UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL +3 DAY))");
		return $result;
	}
	public function updateUserDetails($arr) {
                $update  =  $this->db->query("UPDATE msg_schedule_master set msg_send_status =  :msg_status where user_id = :user_id and id = :id ",$arr);
                if($update==1){
                        return 1;
                }else{
                        return 0;
                }
        }

}
$notObj = new Notification($db); 
//get all three details and create one array 
$notArr1  = $notObj->getNotificationDet();
$notArr2  = $notObj->getPregNotificationDet();
$notArr3  = $notObj->getHealthNotificationDet();

//print_r($notArr1);
//print_r($notArr2);
$notArr = array_merge_recursive($notArr1,$notArr2,$notArr3);
print_r($notArr);
//exit;
if(sizeof($notArr)>0){
	$baseUrl = "http://bulksms.tugbiz.com/api/sendmsg.php?stype=normal&priority=ndnd&user=Tugbizsms&pass=tugbiz1234&sender=LMSRES";
	for($i=0;$i<sizeof($notArr);$i++){
		$conTextval= $param = "";
		$id= $notArr[$i]['id'];
		$user_id = $notArr[$i]['user_id'];
		$start_date = $notArr[$i]['start_date'];
		$vac_name   = $notArr[$i]['vac_name'];
		$sal  = $notArr[$i]['salutation'];
		$first_name = $notArr[$i]['first_name'];
		$last_name  = $notArr[$i]['last_name'];
		$mobile = $notArr[$i]['mobile'];
		$text_msg = $notArr[$i]['text_msg'];
		$child_name = $notArr[$i]['name'];
		$msg_type = $notArr[$i]['msg_type'];
		if(strlen($first_name)>0){
			$conTextval = $first_name;
		}
		if(strlen($last_name)>0){
            $conTextval = $conTextval." ".$last_name;
        }
		if(strlen($conTextval)==0){
            $conTextval = $mobile;
		}

		if(strlen($text_msg)>0){
			if($msg_type=='child'){
				$conTextval = $conTextval.",\n".$text_msg." \n"."for your child ".$child_name." of vaccine  ".$vac_name;
			}else if($msg_type=='pregnancy'){
				$conTextval = $conTextval.",\n".$text_msg." \n"."doctor related to routine checkup  of ".$child_name;
			}else if($msg_type=='health'){
				$conTextval = $conTextval.",\n".$text_msg." \n"."doctor related to routine checkup  of ".$child_name;
			}		
		}
		$conTextval = "Hi ".$conTextval;
		//echo $conTextval."\n";
		$param = "&phone=".$mobile."&text=".urlencode($conTextval);
		$urlToHit = $baseUrl.$param;
		//$responce = file_get_contents($urlToHit);
		$responce = md5($param.time());
		if(strlen($responce)>0){
			$flag = strtolower(substr("S.834839",0,1)); 
			if($flag=="s"){
				 $arrRes   =  array(
                                       "user_id"=> $user_id,
                                       "id"=> $id,
                                       "msg_status"=> "send",
                		);	
			}else{
				$arrRes   =  array(
                                       "user_id"=> $user_id,
                                       "id"=> $id,
                                       "msg_status"=> "failed",
                                );  
			}
		}else{
			$arrRes   =  array(
                                        "user_id"=> $user_id,
                                       "id"=> $id,
                                       "msg_status"=> "other",
                                ); 
		}
		//$notObj->updateUserDetails($arrRes);	
		//print_r($arrRes);
		//echo $urlToHit. "\n";
		echo $message = "responce===".$$responce."<br>===urlToHit====".$urlToHit."<br>";
 		
		//print_r($db);
	}
}

?>
