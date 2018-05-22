<?php
require("../includes/Db.class.php");
require("../classes/Doctor.php");
require("../classes/Validation.php");
$mode 			= isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "";
if($mode=='save' || $mode=='update'){
	$givenInputArr	= $_POST;
}else{
	$givenInputArr	= $_REQUEST;	
}
$inputArr       = Validation::validateUserInput($givenInputArr);
switch($mode){
	case "save" :
			$arr = saveDoctorInfo($inputArr);
			break;
	case "update" :
			$arr = updateDoctorInfo($inputArr);
			break;
	default: 
			$arr = getDocDetails($inputArr);
}
//header('Content-type: application/json');
//echo $_REQUEST['callback'] . '(' . json_encode($arr) . ');';
echo json_encode($arr);
exit;
function updateDoctorInfo($inputArr){
	try {
			global $db;
			//server side validation
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			$docObj 		= new Doctor($db);
			$r_user 		= $docObj->updateDocDetails($keyArr);
			$arr = array('status' =>$r_user);
			return $arr;
		}catch(Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
}
function getDocDetails($inputArr){
	try {
			global $db;
			//server side validation
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			//print_r($keyArr);
			$docObj 		= new Doctor($db);
			$r_user 		= $docObj->getDocDetails($keyArr['mobile']);
					
			if(is_array($r_user) && sizeof($r_user)>0){
				$today 	 = date("Y-m-d");
				$time 	 = strtotime("-20 year",time());
				$mindate = date("Y-m-d", $time);
				$r_user[0]['min_date'] = $mindate;
				$r_user[0]['max_date'] = $today;
				
				$arr = array(
					'status' => "1",
					'data' => $r_user,
				);
			}else {
				$arr = array('status' => "0");
			}
			return $arr;
		}catch(Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
}
function saveDoctorInfo($inputArr){
		try {
			global $db;
			//server side validation
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			//print_r($keyArr);
			$docObj 		= new Doctor($db);
			$r_user 		= $docObj->getDocDetails($keyArr['mobile_num']);
			//print_r($r_user);
			if(is_array($r_user) && sizeof($r_user)>0){
				$arr = array('status' => "Mobile Number already exits!");
			}else{
				$row_doc = $docObj->insertDocDetails($keyArr);
				if($row_doc>0){
					$arr = array('status' => 1);
				}else{
					$arr = array('status' => 0);
				}
			}	
			return $arr;
		}catch(Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
}		

?>
