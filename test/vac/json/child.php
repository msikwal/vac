<?php
require("../includes/Db.class.php");
require("../classes/User.php");
require("../classes/ChildDetails.php");
require("../classes/Validation.php");
$mode 			= $_REQUEST['mode'];
if($mode=='save'){
	$givenInputArr	= $_POST;
}else{
	$givenInputArr	= $_REQUEST;	
}
$inputArr       = Validation::validateUserInput($givenInputArr);

switch($mode){
	case "save" :
			$arr = saveUserInfo($inputArr);
			break;
	case "vac" :
			$arr = getVacInfo($inputArr);
			break;
	case "vacChart" : 
			$arr = getVacChartDetails($inputArr);
			break;			
	default: 
			$arr = AuthUser($mobile,$password);
}
header('Content-type: application/json'); 
echo json_encode($arr,true);
exit;
function getVacChartDetails($inputArr){
	try {
			global $db;
			
			$childObj = new ChildDetails($db);
			$row_child   = $childObj->getChildVacChartDetails();
			if(is_array($row_child) && sizeof($row_child)>0){
				
				//print_r($childArr);
				$arr = array(
					'status' => 1,
					'data' => $row_child
				);
			}else{
				$arr = array('status' => 0);
			}
			return $arr;
		}catch(Exception $e) {
			echo 'Caught exception: Please try after sometime.\n';
		}
}
function getVacInfo($inputArr){
		try {
			global $db;
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			$childObj = new ChildDetails($db);
			$row_child   = $childObj->getChildVacDetails($keyArr);
			if(is_array($row_child) && sizeof($row_child)>0){
				$childArr  = getFormatedArr($row_child);
				//print_r($childArr);
				$arr = array(
					'status' => 1,
					'data' => $childArr
				);
			}else{
				$arr = array('status' => 0);
			}
			return $arr;
		}catch(Exception $e) {
			echo 'Caught exception: Please try after sometime.\n';
		}
}
function getFormatedArr($arr){
	$returnArr = array();
	$oldBirthDate = '';
	$today = strtotime(date("Y-m-d H:i:s"));
	for($i=0;$i<sizeof($arr);$i++){
		$childName = $arr[$i]['child_name'];
		$birthDate = $arr[$i]['birth_date'];
		$end_date  = strtotime($arr[$i]['end_date']);
		$vacId 		= $arr[$i]['vac_id'];
		if($end_date > $today){
			$arr[$i]['vac_status'] = "success";
		}else{
			$arr[$i]['vac_status'] = "danger";
		}
		//$returnArr[$birthDate][$vacId] = $arr[$i];
		
		if($birthDate!==$oldBirthDate){
			$oldBirthDate = $birthDate;
			$key = $birthDate."_".$vacId;
			$returnArr[$birthDate] = array();
					
		}
		array_push($returnArr[$birthDate],$arr[$i]);
	}
	//echo "<pre>";
	//print_r($returnArr);
	//echo "</pre>";
	//exit;
	return $returnArr;
}
function saveUserInfo($inputArr){
		try {
			global $db;
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			$userObj 		= new User($db);
			$r_user 		= $userObj->getUserDetails($keyArr['mobile_num']);
			if(is_array($r_user) && sizeof($r_user)>0){
				$user_id = $r_user[0]['user_id'];
			}else{
				$i_user 		= $userObj->insertUserDetails($keyArr);
				$user_id 		= $db->lastInsertId();
			}
			$keyArr['parentId'] = $user_id;
			$childObj = new ChildDetails($db);
			$row_child   = $childObj->insertChildDetails($keyArr);
			if($row_child>0){
				$arr = array('status' => $row_child);
			}else{
				$arr = array('status' => 0);
			}
			return $arr;
		}catch(Exception $e) {
			echo 'Caught exception: Please try after sometime.\n';
		}
}		

?>
