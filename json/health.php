<?php
require("../includes/Db.class.php");
require("../classes/User.php");
require("../classes/HealthDetails.php");
require("../classes/Validation.php");
$mode 			= $_REQUEST['mode'];
$givenInputArr	= $_POST;
$inputArr       = Validation::validateUserInput($givenInputArr);
switch($mode){
	case "save" :
			$arr = saveHealthInfo($inputArr);
			break;
	default: 
			$arr = AuthUser($mobile,$password);
}
header('Content-type: application/json');
echo json_encode($arr,true);
exit;

function saveHealthInfo($inputArr){
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
			$pregObj = new HealthDetails($db);
			$row_child   = $pregObj->insertHealthDetails($keyArr);
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
