<?php
namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;
class ShareresultDAO{

	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{

		$this->tableGateway = $tableGateway;
	}

	public function insertRow($email,$type){
	
		$arr=array("email"=>$email,"type"=>$type);
	
		$affectedRows=$this->tableGateway->insert($arr);
	}
}