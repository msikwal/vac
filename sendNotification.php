<?php
require("../includes/Db.class.php");
require("../classes/User.php");

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
