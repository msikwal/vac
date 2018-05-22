<?php
require("../includes/Db.class.php");
require("../classes/User.php");
$mode 			= isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "";
$inputArr		= $_REQUEST;
switch($mode){
	case "save" :
			$arr = saveUserInfo($inputArr);
			break;
	case "update" :
			$arr = updateUserInfo($inputArr);
			break;
	default: 
			$arr = getUserDetails($inputArr);
}
header('Content-type: application/json');
//echo $_REQUEST['callback'] . '(' . json_encode($arr) . ');';
echo json_encode($arr);
exit;
function updateUserInfo($inputArr){
	try {
			global $db;
			//server side validation
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			$docObj 		= new User($db);
			$r_user 		= $docObj->updateUserDetails($keyArr);
			$arr = array('status' =>$r_user);
			return $arr;
		}catch(Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
}
function getUserDetails($inputArr){
	try {
			global $db;
			//server side validation
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			//print_r($keyArr);
			$docObj 		= new User($db);
			$r_user 		= $docObj->getUserDetails($keyArr['mobile']);
					
			if(is_array($r_user) && sizeof($r_user)>0){
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
?>
