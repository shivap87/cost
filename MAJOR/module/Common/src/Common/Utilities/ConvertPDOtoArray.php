<?php
namespace Common\Utilities;
use Zend\Db\ResultSet\ResultSet;
class ConvertPDOtoArray {

	
		public static function convertNewsPDOtoNewsArray(ResultSet $pdo){
		$result=array();
		$i=0;
		foreach ($pdo as $k){
			
			$result[$i]['id']=$k->id;
			$result[$i]['name']=$k->name;
			$i++;
		}
		return $result;
	}

		
}

?>