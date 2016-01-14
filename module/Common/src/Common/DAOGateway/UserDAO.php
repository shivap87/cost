<?php
namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;
use Common\Utilities\Constants;
class UserDAO
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
	
	public function insertStudent($username,$password,$key,$role,$regchannel){
		
		$gensubkey = md5(date('Y-m-d H:i:s')).'G';
		$usersubkey = md5(date('Y-m-d H:i:s')).'U';
		
		// Check for existing mail id 
		$resultSet = $this->tableGateway->select(array('email_id' => $username));
		if(count($resultSet)>0){
			$this->tableGateway->update(array('user_password'=>md5($password),'tbl_role_role_id'=>$role,'verificationkey'=>$key,'usersubkey'=>$usersubkey,'gensubkey'=>$gensubkey),array('email_id' => $username));
		}else{
			$arr=array('email_id'=>$username,'user_password'=>md5($password),'tbl_role_role_id'=>$role,'verificationkey'=>$key,'usersubkey'=>$usersubkey,'gensubkey'=>$gensubkey,"regchannel"=>$regchannel);
			$this->tableGateway->insert($arr);
			
		}
		
		$resultSet = $this->tableGateway->select(array('email_id' => $username));
		$row=$resultSet->current();
		return $row->user_id;
		
	}
	
	public function updateLastLogin($uid){
		date_default_timezone_set('America/Los_Angeles');
		$date = date('Y-m-d H:i:s');
		$rowsaffected=$this->tableGateway->update(array('lastlogin' => $date),array('user_id' => $uid));
		$flag = ($rowsaffected == 1) ? true : false;
		return $flag;
	}
	
	public function updateUnSubscription($key,$mode){
		
		$rowsaffected=0;
		if($mode=="g"){
		$rowsaffected=$this->tableGateway->update(array('isgensub' => "0"),array('gensubkey' => $key));
		}
		if($mode=="u"){
			$rowsaffected=$this->tableGateway->update(array('isusersub' => "0"),array('usersubkey' => $key));
			//$this->getSQLQueryString();
		}
		$flag = ($rowsaffected == 1) ? true : false;
		return $flag;
		
	}
	
	public function updateVerification($key){
		date_default_timezone_set('America/Los_Angeles');
		$date = date('Y-m-d H:i:s');
		$rowsaffected=$this->tableGateway->update(array('isverified' => "1",'regdate'=>$date),array('verificationkey' => $key));
		$flag = ($rowsaffected == 1) ? true : false;
		return $flag;
	}
	
	public function checkOldPassword($id,$password){
		$resultSet = $this->tableGateway->select(array('user_id' => $id,'user_password'=>md5($password)));
		if(count($resultSet)>0){
			return 1;
		}else{
			return 0;
		}
	}
	
	public function getUserByUID($uid){
		$resultSet = $this->tableGateway->select(array('user_id' => $uid));
		return $resultSet;
	}
	public function getUserByKeyAndEmail($key,$email){
		
		$resultSet = $this->tableGateway->select(array('email_id' => $email,"verificationkey"=>$key));
		
		if(count($resultSet)>0){
			return true;
		}else{
			return false;
		}
		
	}
	
	public function updatePassword($id,$newpassword){
		$rowsaffected=$this->tableGateway->update(array('user_password' => md5($newpassword)),array('user_id' =>$id ));
		$flag = ($rowsaffected == 1) ? true : false;
		return $flag;
	}
	
	public function updateKeyByEmail($email,$key){
	
		$rowsaffected=$this->tableGateway->update(array('verificationkey' => $key),array('email_id' =>$email ));
		$flag = ($rowsaffected == 1) ? true : false;
		return $flag;
	}
	
	
	
	public function insertGuest($username){
	
		$arr=array('email_id'=>$username,'tbl_role_role_id'=>5);
		return $this->tableGateway->insert($arr);
	
	}
	
	public function getSQLQueryString(){
		return $this->tableGateway->getSql()->getSqlPlatform()->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}
	
	public function checkEmailAvailable($username){
		$resultSet = $this->tableGateway->select(array('email_id' => $username,'tbl_role_role_id != 5'));
		
		if(count($resultSet)>0){
			return true;
		}else{
			return false;
		} 
	}
	
	public function resetByEmailandKey($email,$key,$password){
		$rowsaffected=$this->tableGateway->update(array('user_password' =>md5($password),'isverified'=>'1'),array('email_id' =>$email,"verificationkey"=>$key));
		$key=md5(date('Y-m-d hh:mm:ss'));
		$rowsaffected=$this->tableGateway->update(array('verificationkey' =>$key),array('email_id' =>$email));
	}
	
	public function checkLogin($username,$password){
		$resultSet = $this->tableGateway->select(array('email_id' => $username,'user_password'=>md5($password),'isverified'=>'1'));
		
		
		//echo $this->getSQLQueryString();
		
		return $resultSet;
	}
	
}