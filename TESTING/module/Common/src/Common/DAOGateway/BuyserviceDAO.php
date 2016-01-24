<?php

namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class BuyserviceDAO
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function insertBuyer($input){	    
	    $affectedRows=$this->tableGateway->insert($input);
	    $id = $this->tableGateway->lastInsertValue;
	    return $id;	   
	}

	public function is_service_accessed($uid){
		$resultSet = $this->tableGateway->select(array('user_id' => $uid,'is_accessed'=>'no'));		
		return $resultSet;	
	}
	
	public function service_status_update($id){
		$rowsaffected=$this->tableGateway->update(array('is_accessed' => 'yes'),array('id' => $id));		
		return $rowsaffected;
	}
	
	public function getAllBuyerData(){
	    $resultSet = $this->tableGateway->select();	    
	    return $resultSet;
	}
	
	public function check_order($uid,$transaction_id,$payment_date){
		$resultSet = $this->tableGateway->select(array('user_id' => $uid,'transaction_id'=>$transaction_id,'payment_date'=>$payment_date));		
		return $resultSet;
	}
	
	public function getSQLQueryString(){
		return $this->tableGateway->getSql()->getSqlPlatform()->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}
	
	
}
