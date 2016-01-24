<?php

namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class PlanDAO
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function insertUID($uid){
		$arr=array("uid"=>$uid);
		$affectedRows=$this->tableGateway->insert($arr);
	}
	
	public function getPlanByUID($uid){
		$resultSet = $this->tableGateway->select(array('uid' => $uid));
		//var_dump($resultSet);
		//echo $this->getSQLQueryString();
		return $resultSet;
	}
	
public function insertPersonal($uid,$val){
$rowsaffected=$this->tableGateway->update(array('personal' => $val),array('uid' => $uid));
}
	
public function updateMyProfile($uid){
		$rowsaffected=$this->tableGateway->update(array('myprofile' => "1"),array('uid' => $uid));
	}	
public function updatePersonal($uid){
	$rowsaffected=$this->tableGateway->update(array('personal' => "1"),array('uid' => $uid));
}	

public function updateactivities($uid){
	$rowsaffected=$this->tableGateway->update(array('activities' => "1"),array('uid' => $uid));
}


public function updateGPA($uid){
	
	$rowsaffected=$this->tableGateway->update(array('gpa' => "1"),array('uid' => $uid));
	
}

public function updateMajor($uid){
	$rowsaffected=$this->tableGateway->update(array('major' => "1"),array('uid' => $uid));
}

public function updateMyUCEAZY($uid){
	$rowsaffected=$this->tableGateway->update(array('mycounselor' => "1"),array('uid' => $uid));
}

public function updateCost($uid){
	$rowsaffected=$this->tableGateway->update(array('costs' => "1"),array('uid' => $uid));
}

public function updateTesting($uid){
	$rowsaffected=$this->tableGateway->update(array('testing' => "1"),array('uid' => $uid));
}
	
	public function getSQLQueryString(){
		return $this->tableGateway->getSql()->getSqlPlatform()->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}
	
}
