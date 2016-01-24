<?php
namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;
class AdvancefeedbackDAO{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{

		$this->tableGateway = $tableGateway;
	}

	public function insertRow($a1,$a2,$a3,$a4,$a5,$s1,$s2,$s3,$s4,$s5){

		$arr=array("a1"=>$a1,"a2"=>$a2,"a3"=>$a3,"a4"=>$a4,"a5"=>$a5,"s1"=>$s1,"s2"=>$s2,"s3"=>$s3,"s4"=>$s4,"s5"=>$s5);

		$affectedRows=$this->tableGateway->insert($arr);
	}
}