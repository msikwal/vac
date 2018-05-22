<?php
class ChildDetails
{
	private $db;
	public function __construct($db)
	{ 			
		$this->db = $db;	
	}
	public function getChildVacDetails($arr){
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
	
	public function getChildVacChartDetails(){
		$result = $this->db->query("SELECT * FROM  vac_details");
		return $result;
	}
	public function getChildDetails($arr){
		$result = $this->db->query("SELECT * FROM  child_details WHERE  birth_date= :birth_date AND parent_id = :parent_id",$arr);
		return $result;
	}
	public function searchChildDetails($id=""){
		if($id){
			$array = array("doc_id"=>$id);
			$result = $this->db->query("SELECT * FROM  child_details WHERE child_id = :id",$array);
		}else{
			$result = $this->db->query("SELECT * FROM  child_details");
		}
		return $result;
	}
	public function updateChildDetails($arr){
		//$arr 	=  array("doc_name"=>"msikwal","doc_id"=>"1")
		$update	=  $this->db->query("UPDATE child_details SET child_name = :child_name WHERE child_id = :child_id",$arr); 
		return $update;
	}
	public function insertChildDetails($arrInput){
		try{
			
			$arr 	=  array(
						"child_name"=>$arrInput['childern_name'],
						"birth_date"=>$arrInput['birth_date'],
						"parent_id"=>$arrInput['parentId'],
						"gender" =>$arrInput['gender']
			);
			$inputSel 	=  array(
						"birth_date"=>$arrInput['birth_date'],
						"parent_id"=>$arrInput['parentId']
			);
			$doc_id = $arrInput['docId'];
			$userDetails = $this->getChildDetails($inputSel);			
			if(is_array($userDetails) && sizeof($userDetails)>0){
				return 2;
			}else{
				$insert	 =  $this->db->query("INSERT INTO child_details(child_name,birth_date,parent_id,gender) VALUES(:child_name,:birth_date,:parent_id,:gender)",$arr); 		
				$child_id = $this->db->lastInsertId();
								
				$result = $this->db->query("SELECT * FROM  vac_details");
				$createVacArr = array();
				if(is_array($result)){
					$length = sizeof($result);
					$birthDate  = strtotime($arr['birth_date']);
					for($i=0;$i<$length;$i++){
						$vacId = $result[$i]['vac_id'];
						$vacName = $result[$i]['vac_name'];
						$recomended_days = $result[$i]['recomended_by_doc'];

						if($recomended_days!=""){
							$addEndMonth = "+".$recomended_days;

							$vacEndDate 	 = date("Y-m-d",strtotime($addEndMonth,$birthDate));
							
							$vacStartDate =  date('Y-m-d',(strtotime ( '-3 day' , strtotime ($vacEndDate) ) ));
						}else{
							$vacEndDate = $vacStartDate = date('Y-m-d',$birthDate);				
						}

						$createVacArr[$i] = array(
							'user_id' => $arr['parent_id'],
							'msg_id' => $child_id,
							'doc_id' => $doc_id,
							'vac_id' => $vacId,
							'msg_type' => 'child',
							'start_date'=> $vacStartDate,
							'end_date' => $vacEndDate
								
						);
					}
				}
				
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
