<?php
class UserLoginDetails
{
	private $db;
	public function __construct($db)
	{ 			
		$this->db = $db;	
	}
	public function validateUserDetails($inputArr){
			
		$array = array(
					"mobile"=>isset($inputArr['mobile_num']) ? $inputArr['mobile_num'] : "" ,
					"password"=>isset($inputArr['u_pass']) ?$inputArr['u_pass'] : "",
				 );
				
		$result = $this->db->query("SELECT group_id FROM  user_login_details WHERE user_mobile = :mobile and password = :password",$array);
		return $result;
	}
	public function updateUserLogInDetails($arr){
		//$arr 	=  array("user_name"=>"msikwal","user_id"=>"1")
		$update	=  $this->db->query("UPDATE user_login_details SET user_name = :user_name WHERE user_id = :user_id",$arr); 
		return $update;
	}
	public function insertUserLogInDetails($input_arr){
			
			$arr 	=  array(
					"user_mobile"=>$input_arr['u_mobile'],
					"user_name"=>$input_arr['u_name'],
					"password"=>$input_arr['u_password'],
					"group_id" => $input_arr['group_id']
			);
			$insert	 =  $this->db->query("INSERT INTO user_login_details(user_mobile,user_name,password,group_id) VALUES(:user_mobile,:user_name,:password,:group_id)",$arr); 
			return $insert;
	}
}
?>