<?php
require("../classes/UserLoginDetails.php");
class User
{
	private $db;
	public function __construct($db)
	{ 			
		$this->db = $db;	
	}
	public function getUserDetails($mobile=""){
		if($mobile){
			$array = array("mobile"=>$mobile);
			$result = $this->db->query("SELECT * FROM  user_details WHERE mobile = :mobile",$array);
		}else{
			$result = $this->db->query("SELECT * FROM  user_details");
		}
		return $result;
	}
	public function updateUserDetails($arr){
		
		$arrUser 	=  array(
					"first_name"=>isset($arr['first_name']) ? $arr['first_name'] : "" ,
					"last_name"=>isset($arr['last_name']) ? $arr['last_name'] : "" ,
					"uid"=>isset($arr['user_id']) ? $arr['user_id'] : "" ,
					//"password"=>isset($arr['u_pass']) ? $arr['u_pass'] : "",
					"email" => isset($arr['email']) ? $arr['email'] : "",
					"address" =>isset($arr['address']) ? $arr['address'] : "" ,
					//"pincode" =>isset($arr['pincode']) ? $arr['pincode'] : "" ,
					//"location" =>isset($arr['location']) ? $arr['location'] : "" ,
					//"phone" =>"",
					//"text_msg" =>isset($arr['wel_msg']) ? $arr['wel_msg'] : "" 
					//"per_patient_amt" =>isset($arr['per_patient_amt']) ? $arr['per_patient_amt'] : "50" ,
					
					
		);
		//echo"<pre>";
		//print_r($arr);
		//print_r($arrUser);
		//echo"</pre>";
		//exit;
		$update	 =  $this->db->query("UPDATE user_details set first_name = :first_name,last_name = :last_name,email = :email, address = :address  where user_id = :uid ",$arrUser); 
		
		if($update==1){
			return 1;
		}else{
			return 0;
		}
		
	}
	public function insertUserDetails($input_arr){
		$authObj 	= new UserLoginDetails($this->db);
		$arrLogin 	=  array(
			"u_mobile"=>$input_arr['mobile_num'],
			"u_name"=>isset($input_arr['user_name']) ? 	$input_arr['user_name'] : "",
			"u_password"=>$input_arr['mobile_num'],
			"group_id" => "2"
		);
		
		$authObj->insertUserLogInDetails($arrLogin);
		$today = date("Y-m-d H:i:s");
		$arr 	=  array(
				"salutation"=>"Mr.",
				"first_name"=>"",
				"last_name"=>"",
				"mobile"=>$input_arr['mobile_num'],
				"refer_by_doc"=>$input_arr['docId'],
				"email" => "test@test.com",
				"address" =>"",
				"pincode" =>0,
				"location" =>"",
				"phone" =>0,
				"registration_date" =>$today,
				"group_id" => "2"
		);
		$insert	 =  $this->db->query("INSERT INTO user_details(salutation,first_name,last_name,mobile,refer_by_doc,email,address,pincode,location,phone,registration_date,group_id) VALUES(:salutation,:first_name,:last_name,:mobile,:refer_by_doc,:email,:address,:pincode,:location,:phone,:registration_date,:group_id)",$arr); 
		return $insert;
	}
}
?>
