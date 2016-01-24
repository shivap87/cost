<?php
namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class SimplefeedbackDAO{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{

		$this->tableGateway = $tableGateway;
	}

	public function insertRow($q1,$q2,$q3,$q4,$q5,$q6,$q7){

		$arr=array("q1"=>$q1,"q2"=>$q2,"q3"=>$q3,"q4"=>$q4,"q5"=>$q5,"q6"=>$q6,"q7"=>$q7);

		$affectedRows=$this->tableGateway->insert($arr);
	}
}