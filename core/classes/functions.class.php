<?php 

/**
 *@Author : Razak Zakari (Razzbee)
 * @Email : razzsense@gmail.com
 * @Whatsapp : +233244827491
 * @Facebook : facebook.com/razzbee
 * @File : functions.class.php 
 * @Class : functions
 * @description : Contains general purpose class methods ...
 * @license : see license.txt
 */

class functions {
	
	
	///////////another way of returning an array alert
public static function get_alert($alert_type,$alert_msg,$extra_data=null){

$alert_data = array("alert_type"=>$alert_type,"alert_msg"=>$alert_msg);

//merge the extra dat with the alert data
if(is_array($extra_data)){
$alert_data = array_merge($alert_data,$extra_data);
}//end extra data stuff

//return the alert data
return $alert_data;
}//end get alert


public static function json_alert($alert_type,$alert_msg,$extra_data=null){
	return json_encode(self::get_alert($alert_type,$alert_msg,$extra_data));
}//end json alert

//ajax alert
public static function ajax_alert($alert_type,$alert_msg,$extra_data=null){
	ob_end_clean();
	echo json_encode(self::get_alert($alert_type,$alert_msg,$extra_data));
	exit();
}//end json alert


}

?>