<?php
class PreDetails
{
	private $db;
	public function __construct($db)
	{ 			
		$this->db = $db;	
	}
	public function getPregDetails($arr){
		$childArr['user_id'] = $arr['user_id'];
		/*$result = $this->db->query("SELECT a.child_name, a.birth_date, b . * , c.vac_name
				FROM child_details a, msg_schedule_master b, vac_details c
				WHERE a.parent_id = b.user_id
				AND b.msg_type = 'child'
				AND b.vac_id = c.vac_id
				AND a.parent_id = :user_id
				ORDER BY a.birth_date DESC ,b.end_date ASC",$childArr);*/


		$result = $this->db->query("SELECT a.child_name, a.birth_date, b.user_id,b.id, b.vac_id, b.msg_type, b.start_date, b.end_date, b.given_date, b.msg_send_status, c.vac_name 
			FROM child_details a, msg_schedule_master b, vac_details c 
			WHERE a.parent_id = b.user_id 
            AND a.child_id = b.msg_id
			AND b.msg_type = 'child' 
			AND b.vac_id = c.vac_id 
			AND a.parent_id = :user_id
		    ORDER BY a.birth_date DESC",$childArr);
		
		return $result;
	}
	
	public function getPreDetails($arr){
		//print_r($arr);
		//$result = $this->db->query("SELECT * FROM  pregenancy_details WHERE user_id = :user_id AND UNIX_TIMESTAMP(:preg_start_date) >= UNIX_TIMESTAMP(DATE_ADD(preg_end_date,INTERVAL -3 DAY) ) AND UNIX_TIMESTAMP(:preg_end_date) <= UNIX_TIMESTAMP(DATE_ADD(preg_end_date,INTERVAL +3 DAY) ) ",$arr);
		$result = $this->db->query("SELECT * FROM  pregenancy_details 
			WHERE user_id = :user_id
			AND UNIX_TIMESTAMP(preg_start_date) <= UNIX_TIMESTAMP(CURDATE()) 
			AND UNIX_TIMESTAMP(CURDATE()) < UNIX_TIMESTAMP(preg_end_date) ",$arr);
		return $result;
	}

	public function updateChildDetails($arr){
		//$arr 	=  array("doc_name"=>"msikwal","doc_id"=>"1")
		$update	=  $this->db->query("UPDATE pregenancy_details SET mother_name = :mother_name WHERE mother_name = :mother_name",$arr); 
		return $update;
	}
	
	public function insertPregDetails($arrInput){
		try{
			
			$arr 	=  array(
						"mother_name"=>$arrInput['mother_name'],
						"interval_days"=>$arrInput['interval'],
						"user_id"=>$arrInput['parentId'],
						"current_month" =>$arrInput['currentMonth'],
						"preg_start_date" => $arrInput['expectedStartDate'],
						"preg_end_date"  => $arrInput['expectedDate']
			);
			
			$inputSel 	=  array(
						"user_id"=>$arrInput['parentId'],
						//"preg_start_date"  => $arrInput['expectedDate'],
						//"preg_end_date"  => $arrInput['expectedDate']
			);
			$doc_id = $arrInput['docId'];
			$expectedDate  = $arrInput['expectedDate'];
			//Find a record of user using given  expected date ...
			$userDetails = $this->getPreDetails($inputSel);
			
			if(is_array($userDetails) && sizeof($userDetails)>0){
				return 2;
			}else{
				$insert	 =  $this->db->query("INSERT INTO pregenancy_details(mother_name,interval_days,user_id,current_month,preg_start_date,preg_end_date) VALUES(:mother_name,:interval_days,:user_id,:current_month,:preg_start_date,:preg_end_date)",$arr); 		
				$preg_id = $this->db->lastInsertId();
				$createVacArr = array();
				//make
				//date('Y-m-d',(strtotime ( '+3 day' , strtotime ($pre_start_date) ) ))
				//print_r($arr['interval_days']);
				$today = date("Y-m-d");
				$numberVal  = $arr['interval_days'] * 86400;
				$dateRangeArr  = range(strtotime($today),strtotime($expectedDate), $numberVal);
				array_walk_recursive($dateRangeArr, function(&$element) { $element = date("Y-m-d", $element); });
				//print_r ($arr);
				//exit;
				$length = sizeof($dateRangeArr);
				if(sizeof($dateRangeArr)>0){
					for($i=0;$i<$length;$i++){
						$pre_start_date = $dateRangeArr[$i];
						$pre_end_date =  date('Y-m-d',(strtotime ( '+3 day' , strtotime ($pre_start_date) ) ));
						$createVacArr[$i] = array(
							'user_id' => $arr['user_id'],
							'msg_id' => $preg_id,
							'doc_id' => $doc_id,
							'vac_id' => $i,
							'msg_type' => 'pregnancy',
							'start_date'=> $pre_start_date,
							'end_date' => $pre_end_date
						);
					}
				}
				//print_r($createVacArr);
				//exit;
				for($i=0;$i<sizeof($createVacArr);$i++){
					//to skip the first message 
					if($i==0){
						continue;
					}
					$inputArr = $createVacArr[$i];
					$insertVac  =  $this->db->query("INSERT INTO msg_schedule_master(user_id,msg_id,doc_id,vac_id,msg_type,start_date,end_date) VALUES(:user_id,:msg_id,:doc_id,:vac_id,:msg_type,:start_date,:end_date)",$inputArr); 
				}
				return $insert;
			}
			
		}catch(Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		
	}
}
?>
