<?php
class HealthDetails
{
	private $db;
	public function __construct($db)
	{ 			
		$this->db = $db;	
	}

	
	public function getHealthDetails($arr){
		//print_r($arr);
		//$result = $this->db->query("SELECT * FROM  pregenancy_details WHERE user_id = :user_id AND UNIX_TIMESTAMP(:preg_start_date) >= UNIX_TIMESTAMP(DATE_ADD(preg_end_date,INTERVAL -3 DAY) ) AND UNIX_TIMESTAMP(:preg_end_date) <= UNIX_TIMESTAMP(DATE_ADD(preg_end_date,INTERVAL +3 DAY) ) ",$arr);
		$result = $this->db->query("SELECT * FROM  patient_details WHERE user_id = :user_id
			AND UNIX_TIMESTAMP(sub_start_date) <= UNIX_TIMESTAMP(CURDATE()) 
			AND UNIX_TIMESTAMP(CURDATE()) < UNIX_TIMESTAMP(sub_end_date) ",$arr);
		return $result;
	}

	public function updateHealthDetails($arr){
		//$arr 	=  array("doc_name"=>"msikwal","doc_id"=>"1")
		$update	=  $this->db->query("UPDATE patient_details SET patient_name = :patient_name WHERE patient_name = :patient_name",$arr); 
		return $update;
	}
	
	public function insertHealthDetails($arrInput){
		try{
			$today = date("Y-m-d");
			$addStep  = '+'.$arrInput['currentMonth'].' days';
			$subEndDate =  date('Y-m-d',(strtotime($addStep, strtotime ($today))));

			$arr 	=  array(
						"patient_name"=>$arrInput['patient_name'],
						"interval_days"=>$arrInput['duration'],
						"user_id"=>$arrInput['parentId'],
						"subscription_days" =>$arrInput['currentMonth'],
						"sub_start_date"=>$today,
						"sub_end_date"=>$subEndDate
			);
			
			$inputSel 	=  array(
						"user_id"=>$arrInput['parentId']
			);
			$doc_id = $arrInput['docId'];
			
			//Find a record of user using given  expected date ...
			$userDetails = $this->getHealthDetails($inputSel);
			//print_r($userDetails);
			if(is_array($userDetails) && sizeof($userDetails)>0){
				return 2;
			}else{
				$insert	 =  $this->db->query("INSERT INTO patient_details(patient_name,interval_days,user_id,subscription_days,sub_start_date,sub_end_date) VALUES(:patient_name,:interval_days,:user_id,:subscription_days,:sub_start_date,:sub_end_date)",$arr); 		
				$patient_id = $this->db->lastInsertId();
				$createVacArr = array();

				
				//print_r($arr['interval_days']);
				
				$numberVal  = $arr['interval_days'] * 86400;
				$dateRangeArr  = range(strtotime($today),strtotime($subEndDate), $numberVal);
				array_walk_recursive($dateRangeArr, function(&$element) { $element = date("Y-m-d", $element); });
				//print_r ($dateRangeArr);
				//exit;
				$length = sizeof($dateRangeArr);
				if(sizeof($dateRangeArr)>0){
					for($i=0;$i<$length;$i++){
						$pre_end_date = $dateRangeArr[$i];
						$pre_start_date =  date('Y-m-d',(strtotime ( '-3 day' , strtotime ($pre_end_date) ) ));
						$createVacArr[$i] = array(
							'user_id' => $arr['user_id'],
							'msg_id' => $patient_id,
							'doc_id' => $doc_id,
							'vac_id' => $i,
							'msg_type' => 'health',
							'start_date'=> $pre_start_date,
							'end_date' => $pre_end_date
						);
					}
				}
				//print_r($createVacArr);
				//exit;
				for($i=0;$i<sizeof($createVacArr);$i++){
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
