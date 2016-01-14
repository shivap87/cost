<?php
namespace Common\Utilities;
use Zend\Ldap\Converter\Converter;
class Convertors{
	
	
	static function getEthnicityVal($key){
		
		$rows = explode(",", Constants::PLAN_MYPROFILE_ETHNICITY);
		foreach ($rows as $row){
			$tmp = explode(":", $row);
			
			if($tmp[0]==$key){
				return $tmp[1];
			}
		}
		return null;
		
	}
	
	
	static function checkReportAccess($email){
		$rows = explode(":", Constants::REPORT_ACCESS);
		foreach ($rows as $row){
			if($row==$email){
				return true;
			}
		}
		return false;
	}
	
	static function getServiceName($key){
		$rows = explode(":", Constants::SERVICES_IDS);
		foreach ($rows as $row){
			$tmp = explode("#", $row);
				
			if($tmp[0]==$key){
				return $tmp[1];
			}
		}
		return "";
	}
	
	
	static function getChannelName($key){
		
		$rows = explode(":", Constants::CHANNEL_IDS);
		foreach ($rows as $row){
			$tmp = explode("#", $row);
				
			if($tmp[0]==$key){
				return $tmp[1];
			}
		}
		return "WEB";
		
	}
	
	
	static function getUS($us){
		
		if($us=="both"){
			return "Both UC and CSU";
		}elseif($us=="uc"){
			return "UC";
		}else{
			return "CSU";
		}
		
		
	}
	
	static function getHrs($val){
		if($val==null || trim($val)==""){
			return "0";
		}else{
			$arr=explode("/", $val);
			return $arr[0];
		}
	}
	
	static function getWeeks($val){
		if($val==null || trim($val)==""){
			return "0";
		}else{
			$arr=explode("/", $val);
			return $arr[1];
		}
	}
	
	static function nullSub($val){
		if($val==null){
			return "false";
		}else{
			return "true";
		}
	}
	static function getCSize($code){
		if($code=='S'){
			return "Small";
		}elseif ($code=="L"){
			return "Large";
		}elseif ($code=="A"){
			return "No Preference";
		}
	}
	
	 static function getCPref($code){
		if($code=='U'){
			return "Urban";
		}elseif ($code=="S"){
			return "Suburban";
		}elseif($code=="R"){
			return "Rural";
		}elseif ($code=="A"){
			return "No Preference";
		}
	}
	static function traverseArray(&$array, $keys) {
		foreach ($array as $key => &$value) {
			if (is_array($value)) {
				Convertors::traverseArray($value, $keys);
			} else {
				if (in_array($key, $keys)){
					unset($array[$key]);
				}
			}
		}
	}
	
	 static function getStateDescription($code){
		if($code=='CA'){
			return "California";
		}elseif ($code=="OUS"){
			return "Outside US";
		}elseif($code=="AUS"){
			return "Another US State";
		}
	}
}