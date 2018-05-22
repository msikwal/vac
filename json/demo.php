<?php
require("../includes/Db.class.php");
require("../classes/Doctor.php");
require("../classes/Validation.php");
$mode 			= isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "";
if($mode=='save_tmp_data'){
	$givenInputArr	= $_POST;
}else{
	$givenInputArr	= $_REQUEST;	
}
$inputArr       = Validation::validateUserInput($givenInputArr);
switch($mode){
	case "save_tmp_data" :
			$arr = saveTempDoctorInfo($inputArr);
			break;
	default: 
			$arr = ["Error."];
}
//header('Content-type: application/json');
//echo $_REQUEST['callback'] . '(' . json_encode($arr) . ');';
echo json_encode($arr);
exit;

function saveTempDoctorInfo($inputArr){
		try {
			global $db;
			//server side validation
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			$docObj 		= new Doctor($db);
			$r_user 		= $docObj->getTempDocDetails($keyArr['mobile_num']);
			//print_r($r_user);
			if(is_array($r_user) && sizeof($r_user)>0){
				$arr = array('status' => 2);
			}else{
				$row_doc_id = $docObj->insertTempDocDetails($keyArr);

				$baseUrl = "http://bulksms2.tugbiz.com/websms/sendsms.aspx?msgType=3&userid=tugbiz&password=1234565&sender=TUGBIZ";
				
				$conTextval = urlencode($keyArr['text_msg']);
				$param 	  = "&mobileno=".$keyArr['mobile_num']."&msg=".$conTextval;
				$urlToHit = $baseUrl.$param;
				//$responce = file_get_contents($urlToHit);
				$responce = "Success : Message ID : 20180521231606000_238000123";
				//$responce = "Failed : Invalid User/Password";
				$responceArr = explode(':',$responce);
				
				if(strlen($responce)>0){
					if(trim($responceArr[0]) =="Success"){
						$arrRes   =  array(
		                   "mobile_num"=> $keyArr['mobile_num'],
		                   "id"=> $row_doc_id,
		                   "msg_status"=> "send"
	                 	);	
					}else{
						$arrRes   =  array(
		                   "mobile_num"=> $keyArr['mobile_num'],
		                   "id"=> $row_doc_id,
		                   "msg_status"=> "failed"
	                 	);	
					}
				}else{
					$arrRes   =  array(
	                   "mobile_num"=> $keyArr['mobile_num'],
	                   "id"=> $row_doc_id,
	                   "msg_status"=> "other"
                 	);			
				}
				$row_doc = $docObj->updateTempDocDetails($arrRes);
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
