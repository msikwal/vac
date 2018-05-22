<?php
class Validation
{
	public static function validateUserInput($givenInputArr){
		try{
		   	if(sizeof($givenInputArr)<=0){
		     	throw new Exception('Error.');
		    }else {
		    	foreach( $givenInputArr as $key => $key_value ){
		    			if($key=='PLAY_ERRORS'){
		    				continue;
		    			}
		    			if(!$key_value || empty($key_value)){
		    				throw new Exception('Required value can not be Null.'.$key);
		    				break;
		    			}
						$inputArr[$key] = $key_value;
				}
				return $inputArr;
		    }
		} catch (Exception $e) {
		    echo $e->getMessage();
		    exit;
		}
	}
}
?>
