<?php
require("../includes/Db.class.php");
require("../classes/ChildDetails.php");

$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . md5(uniqid(mt_rand(), true)) . "@yourhost.test
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:19970714T170000Z
DTEND:19970715T035959Z
SUMMARY:Bastille Day Party
END:VEVENT
END:VCALENDAR";

header("Content-Type: text/x-vCalendar");
header('Content-Disposition: attachment; filename=calendar.vcs');
echo $ical;
exit;

/*class ICS {
    var $data;
    var $name;
    function ICS($start,$end,$name,$description,$location) {
        $this->name = $name;
        $this->data = "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART:".date("Ymd\THis\Z",strtotime($start))."\nDTEND:".date("Ymd\THis\Z",strtotime($end))."\nLOCATION:".$location."\nTRANSP: OPAQUE\nSEQUENCE:0\nUID:\nDTSTAMP:".date("Ymd\THis\Z")."\nSUMMARY:".$name."\nDESCRIPTION:".$description."\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT10080M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\nEND:VCALENDAR\n";
    }
    function save() {
        file_put_contents($this->name.".vcs",$this->data);
    }
    function show() {
        //header("Content-type:text/calendar");
        //header('Content-Disposition: attachment; filename="'.$this->name.'.ics"');
        header("Content-Type: text/x-vCalendar");
		header('Content-Disposition: attachment; filename="'.$this->name.'.vcs"');
        Header('Content-Length: '.strlen($this->data));
        Header('Connection: close');
        echo $this->data;
    }
}

$event = new ICS("2014-09-29 09:00","2014-09-30 21:00","Test Event","This is an event made by msikwal","Mumbai");
$event->show();
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
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
}
function getVacInfo($inputArr){
		try {
			global $db;
			foreach( $inputArr as $key => $key_value ){
				$keyArr[$key] = $key_value;
			}
			//print_r($keyArr);
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
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
}
function getFormatedArr($arr){
	$returnArr = array();
	$oldBirthDate = '';
	for($i=0;$i<sizeof($arr);$i++){
		$childName = $arr[$i]['child_name'];
		$birthDate = $arr[$i]['birth_date'];
		$vacId 		= $arr[$i]['vac_id'];
	
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
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
}		
*/
?>
