<?php
namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class SavedSearchResultsDAO{
	
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
	
	public function insertRow($searchcriteria_id,$resulttype,$collegename){
		
		$arr=array("searchcriteria_id"=>$searchcriteria_id,"resulttype"=>$resulttype,"collegename"=>$collegename);
		
		$affectedRows=$this->tableGateway->insert($arr);
	}
	
	
	
	
	
	
	
	public function insertAdvanceRow($searchcriteria_id,$resulttype,$row){
	
		$arr=array("searchcriteria_id"=>$searchcriteria_id,"resulttype"=>$resulttype,"collegename"=>$row['name'],
				"relativeCOA"=>$row['relativeCOA'],
				"onCampusCOA"=>$row['onCampusCOA'],
				"size"=>$row['size'],
				"universitySystem"=>$row['universitySystem'],
				"environment"=>$row['environment']
		);
	
		$affectedRows=$this->tableGateway->insert($arr);
	}
	
}