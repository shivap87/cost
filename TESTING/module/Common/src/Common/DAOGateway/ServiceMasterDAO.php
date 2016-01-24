<?php

namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class ServiceMasterDAO
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function getAllServices(){
		$resultSet = $this->tableGateway->select(array('is_active' => "yes"));		
		return $resultSet;
	}
	
	public function getServices_data($field,$val){
		$resultSet = $this->tableGateway->select(array($field => $val));		
		return $resultSet;
	}
	
	public function getSQLQueryString(){
		return $this->tableGateway->getSql()->getSqlPlatform()->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}
	
	
}
