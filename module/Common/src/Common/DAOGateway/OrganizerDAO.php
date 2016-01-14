<?php

namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class OrganizerDAO
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function getAllEvents(){
		$resultSet = $this->tableGateway->select();
		//var_dump($resultSet);
		//echo $this->getSQLQueryString();
		return $resultSet;
	}
	
	public function getSQLQueryString(){
		return $this->tableGateway->getSql()->getSqlPlatform()->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}
	
	
}
